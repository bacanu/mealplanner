@extends('layouts.default')

@section('content')
    
<div class="container" id="app" v-cloak>
    <div class="col-sm-3 col-md-push-9 hidden-xs">
        <h4 style="padding: 10px 0;">Meals</h4>
        <form @submit.prevent="saveNewMeal">
            <div class="form-group">
                <label for="name">Name</label>
                <input v-model="newMeal.name" type="text" class="form-control" id="name"
                       placeholder="Omlette">
            </div>
            <button type="button" @click="saveNewMeal" class="btn btn-primary">Submit</button>
        </form>
        <hr>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th></th>
                <th>Name</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="meal in meals">
                <td :class="{'bg-danger': meal.has_ingredients_without_price}">
                    <button @click="assignMealToNextEmptyDaySlot(meal)"> <<</button>
                </td>
                <td :class="{'bg-danger': meal.has_ingredients_without_price}">
                    <a :href="meal.action_urls.edit" >@{{ meal.name }}</a>&nbsp;
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="col-sm-9 col-md-pull-3">
        <div class="visible-xs">
            <h4 style="padding: 10px 0;">Today</h4>
            <div class="row day-item">
                <div class="col-sm-3 date-item">@{{ today.date_readable }}</div>
                <div class="col-sm-3 meal-item" v-for="meal in today.meals">@{{ meal.name }}</div>
            </div>
        </div>
        <div class="visible-xs">
            <button class="btn btn-default" @click="showDaysOnMobile = !showDaysOnMobile">Show Days</button>
        </div>
        <div :class="{'hidden-xs': !showDaysOnMobile}">
            <h4 style="padding: 10px 0;">Days
                <button v-show="planHasChanges" @click="saveDays" class="btn btn-primary">Save</button>
                <button @click="copyFromLastjFilledWeek" class="btn btn-primary">Copy from last</button>
            </h4>
            <div class="row hidden-xs header">
                <div class="col-sm-3">Date</div>
                <div class="col-sm-3" v-for="slot in slots">@{{ slot.name }}</div>
            </div>

            <div class="row day-item" v-for="day in days" :class="{'is-today': day.isToday}">
                <div class="col-sm-3 date-item">@{{ day.date_readable }}</div>
                <div class="col-sm-3 meal-item" v-for="meal in day.meals">@{{ meal.name }} &nbsp;
                    <button v-show="meal.name.length" @click="removeMealFromDay(meal, day)">&times;</button>
                </div>
            </div>
        </div>

        <table class="table table-striped table-bordered">
            <h4>Ingredients</h4>
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


        <a href="{{ route('prices.index') }}" class="btn btn-default">Add prices</a>
    </div>
</div>
<script>
    var DAYS_INDEX = '{{ route("api.days.index") }}';
    var MEALS_INDEX = '{{ route("api.meals.index") }}';
    var MEALS_STORE = '{{ route("api.meals.store") }}';


const app = new Vue({ 
    el: '#app',
    data: {
        newMeal: makeEmptyMeal(),
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
        estimatedTotalPrice: function () {
            var total = 0;

            Object.values(this.ingredients).forEach(function(ingredient) {
                total += ingredient.price_per_unit * ingredient.quantity
            });

            return total.toFixed(2);
        }
    },
    methods: {
        loadAll: function () {
            this.loadDays();
            this.loadMeals();
        },

        loadDays: function () {
            var that = this;
            axios.post(DAYS_INDEX, {
                _method: "GET",
            }).then(function (response) {
                that.days = response.data.days.map(function (data) {
                    return new Day(data);
                });

                that.lastFilledDays = response.data.lastFilledDays.map(function (data) {
                    return new Day(data);
                });

                that.today = that.days.filter(function (day) {
                    return day.isToday;
                })[0];
                that.slots = response.data.slots;
                that.ingredients = response.data.ingredients;
                that.prepStart = response.data.prepStart;
                that.prepEnd = response.data.prepEnd;

            }, function (error) {
                console.error(error);
            });
        },
        saveDays: function () {
            var that = this;

            Promise.all(that.days.map(function (day) {
                return that.saveDay(day);
            })).then(function () {
                that.planHasChanges = false;
            })
        },
        saveDay: function (day) {
            axios.post(day.action_urls.update, {
                _method: "PUT",
                meals: day.meals
            }).then(function (response) {

            }, function (error) {
                console.error(error);
            });
        },
        loadMeals: function () {
            var that = this;
            axios.post(MEALS_INDEX, {
                _method: "GET"
            }).then(function (response) {
                that.meals = response.data;

            }, function (error) {
                console.error(error);
            });
        },
        copyFromLastjFilledWeek: function () {
            var that = this;
            console.log(this.lastFilledDays);
            this.days.forEach(function(day, index) {
                if (isEmptyDay(day)) {
                    var jFilledDay = that.lastFilledDays[index % 7];
                    day.meals = jFilledDay.meals;

                    that.planHasChanges = true;
                }
            });

            function isEmptyDay(day) {
                return day.meals[0].name === '' && day.meals[1].name === '' && day.meals[2].name === '';
            }
        },
        saveNewMeal: function () {
            var that = this;
            axios.post(MEALS_STORE, {
                name: that.newMeal.name
            }).then(function (response) {
                //reset newTransaction
                that.newMeal = makeEmptyMeal();

                that.loadAll();

            }, function (error) {
                console.error(error);
            });
        },
        removeMealFromDay: function (meal, day) {
            var toReplace = day.meals.filter(function (item) {
                return item.name === meal.name && item.day_slot_id === meal.day_slot_id;
            });

            if (!toReplace.length) return;
            toReplace = toReplace[0];
            toReplace.name = '';
            toReplace.id = false;

            this.planHasChanges = true;
        },
        assignMealToNextEmptyDaySlot: function (meal) {
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
    created: function () {
        console.log("created");
        this.loadAll();
    }
});

function makeEmptyMeal() {
    return {
        name: ''
    }
}

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

