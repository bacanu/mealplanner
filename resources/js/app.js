
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example', require('./components/ExampleComponent.vue'));

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
                total += _.round(ingredient.price_per_unit * ingredient.quantity, 2)
            });

            return _.round(total, 2);
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

                this.today = this.days.find((day) => day.isToday );
                this.slots = response.data.slots;
                this.ingredients = response.data.ingredients;
                this.prepStart = response.data.prepStart;
                this.prepEnd = response.data.prepEnd;

            }, (error) => {
                console.error(error);
            });
        },
        saveDays() {
            Promise.all(this.days.map((day) => {
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
                    var FilledDay = this.lastFilledDays[index % 7];
                    day.meals = FilledDay.meals;

                    this.planHasChanges = true;
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
