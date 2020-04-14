@extends('layouts.default')

@section('content')
    
<div class="container-fluid" id="app" v-cloak>

    <div class="row">
        <div class="col col-sm-9 col-md-7">
            <div class="d-block d-sm-none" v-if="!showDaysOnMobile">
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
            <div :class="{'d-none d-sm-block': !showDaysOnMobile}">
                <h4 style="padding: 10px 0;">Days
                    <button v-show="planHasChanges" @click="saveDays" class="btn btn-primary">Save</button>
                    <button @click="copyFromLastFilledWeek" class="btn btn-primary">Copy from last</button>
                </h4>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr class="d-none d-sm-table-row">
                            <td>Date</td>
                            <td v-for="slot in slots">@{{ slot.name }}</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="day in days" :class="{'is-today': day.isToday}">
                            <th>@{{ day.date_readable }}</th>
                            <td v-for="meal in day.meals">
                                @{{ meal.name }}
                                <button class="float-right d-none d-sm-inline" v-show="meal.name.length" @click="removeMealFromDay(meal, day)">&times;</button>
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

        <div class="col col-sm-3 col-md-5 d-none d-sm-block">
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

                        <a href="{{ route("prices.index") }}" class="float-right" title="One or more ingredients from this meal do not have prices" v-if="meal.has_ingredients_without_price">
                            &nbsp;!&nbsp;
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    var DAYS_INDEX = '{{ route("api.days.index") }}';
    var MEALS_INDEX = '{{ route("api.meals.index") }}';
    var MEALS_STORE = '{{ route("api.meals.store") }}';
</script>
@endsection

