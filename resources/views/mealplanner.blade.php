@extends('layouts.default')

@section('content')
    
<div class="container-fluid" id="app" v-cloak>

    <div class="col-sm-9 col-md-7">
        <div class="visible-xs" v-if="!showDaysOnMobile">
            <h4 style="padding: 10px 0;">
                Today
                <button class="btn btn-primary" @click="showDaysOnMobile = !showDaysOnMobile">Show All Days</button>
            </h4>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr><td>@{{ today.date_readable }}</td></tr>
                </thead>
                <tbody>
                    <tr v-for="meal in today.meals"><td>@{{ meal.name }}</td></tr>
                </tbody>
            </table>
        </div>
        <div :class="{'hidden-xs': !showDaysOnMobile}">
            <h4 style="padding: 10px 0;">Days
                <button v-show="planHasChanges" @click="saveDays" class="btn btn-primary">Save</button>
                <button @click="copyFromLastFilledWeek" class="btn btn-primary">Copy from last</button>
            </h4>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr class="hidden-xs">
                        <td>Date</td>
                        <td v-for="slot in slots">@{{ slot.name }}</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="day in days" :class="{'is-today': day.isToday}">
                        <th>@{{ day.date_readable }}</th>
                        <td v-for="meal in day.meals">
                            @{{ meal.name }}
                            <button class="pull-right hidden-xs" v-show="meal.name.length" @click="removeMealFromDay(meal, day)">&times;</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <table class="table table-striped table-bordered">
            <h4>Ingredients for next week
                <a href="{{ route('prices.index') }}" class="btn btn-primary">Edit prices</a>
            </h4>
            <thead>
            <tr>
                <th>Name</th>
                <th>Quantity</th>
                <th>Est Price</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="ingredient in ingredients">
                <td>@{{ ingredient.name }}</td>
                <td>@{{ ingredient.quantity }}</td>
                <td>@{{ (ingredient.quantity * ingredient.price_per_unit).toFixed(2) }} </td>
                <td><input type="checkbox" :value="ingredient.name" name="ingredients[]"></td>
            </tr>
            <tr>
                <td colspan="2">Est total</td>
                <td>@{{ estimatedTotalPrice }}</td>
                <td></td>
            </tr>
            </tbody>
        </table>


        
    </div>

    <div class="col-sm-3 col-md-5 hidden-xs">
        <h4 style="padding: 10px 0;">Meals <button type="button" @click="promptForNewMeal" class="btn btn-primary">Add</button></h4>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th style="width: 50px"></th>
                <th>Name</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="meal in meals">
                <td>
                    <button @click="assignMealToNextEmptyDaySlot(meal)"> <<</button>
                </td>
                <td>
                    <a :href="meal.action_urls.edit" >@{{ meal.name }}</a>&nbsp;

                    <a href="{{ route("prices.index") }}" class="pull-right" title="One or more ingredients from this meal do not have prices" v-if="meal.has_ingredients_without_price">
                        &nbsp;!&nbsp;
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<script>
    var DAYS_INDEX = '{{ route("api.days.index") }}';
    var MEALS_INDEX = '{{ route("api.meals.index") }}';
    var MEALS_STORE = '{{ route("api.meals.store") }}';


const app = new Vue({ 
    el: '#app',
    data: {
        meals: [],
        days: [],
        lastFilledDays: [],
        today: {},
        slots: [],
        ingredients: [],
        planHasChanges: false,
        prepStart: null,
        prepEnd: null,
        showDaysOnMobile: false,
    },
    computed: {
        estimatedTotalPrice() {
            var total = 0;

            Object.values(this.ingredients).forEach(function(ingredient) {
                total += ingredient.price_per_unit * ingredient.quantity
            });

            return total.toFixed(2);
        }
    },
    methods: {
        loadAll() {
            this.loadDays();
            this.loadMeals();
        },

        loadDays() {
            axios.post(DAYS_INDEX, {
                _method: "GET",
            }).then((response) => {
                this.days = response.data.days.map(function (data) {
                    return new Day(data);
                });

                this.lastFilledDays = response.data.lastFilledDays.map(function (data) {
                    return new Day(data);
                });

                this.today = this.days.filter(function (day) {
                    return day.isToday;
                })[0];
                this.slots = response.data.slots;
                this.ingredients = response.data.ingredients;
                this.prepStart = response.data.prepStart;
                this.prepEnd = response.data.prepEnd;

            }, (error) => {
                console.error(error);
            });
        },
        saveDays() {
            var that = this;

            Promise.all(that.days.map((day) => {
                return this.saveDay(day);
            })).then(() => {
                this.planHasChanges = false;
            })
        },
        saveDay(day) {
            axios.post(day.action_urls.update, {
                _method: "PUT",
                meals: day.meals
            }).then((response) => {

            }, (error) => {
                console.error(error);
            });
        },
        loadMeals() {
            axios.post(MEALS_INDEX, {
                _method: "GET"
            }).then((response) => {
                this.meals = response.data;

            }, (error) => {
                console.error(error);
            });
        },
        copyFromLastFilledWeek() {
            this.days.forEach((day, index) => {
                if (isEmptyDay(day)) {
                    var FilledDay = that.lastFilledDays[index % 7];
                    day.meals = FilledDay.meals;

                    that.planHasChanges = true;
                }
            });

            this.saveDays();

            function isEmptyDay(day) {
                return day.meals[0].name === '' && day.meals[1].name === '' && day.meals[2].name === '';
            }
        },
        promptForNewMeal() {
            var value = window.prompt("Meal name?");
            axios.post(MEALS_STORE, {
                name: value
            }).then(() => {
                this.loadAll();
            }, (error) => {
                console.error(error);
            });
        },
        removeMealFromDay(meal, day) {
            var toReplace = day.meals.filter(function (item) {
                return item.name === meal.name && item.day_slot_id === meal.day_slot_id;
            });

            if (!toReplace.length) return;
            toReplace = toReplace[0];
            toReplace.name = '';
            toReplace.id = false;

            this.planHasChanges = true;
        },
        assignMealToNextEmptyDaySlot(meal) {
            var found = false;

            this.days.forEach(function (day) {
                if (found) return;
                day.meals.forEach(function (m) {
                    if (found) return;
                    if (!m.name) {
                        m.name = meal.name;
                        m.id = meal.id;
                        found = true;
                    }
                })
            });

            this.planHasChanges = true;
        }
    },
    created() {
        this.loadAll();
    }
});

function Day(data) {
    load(this, data);

    function load(parent, data) {
        parent.id = data.id;
        parent.date = data.date;
        parent.date_readable = data.date_readable;
        parent.meals = data.meals;
        parent.action_urls = data.action_urls;
        parent.isToday = data.isToday;
    }
}

</script>
@endsection

