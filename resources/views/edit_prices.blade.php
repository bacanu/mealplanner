@extends('layouts.default')

@section('content')

<div class="container" id="app">
    <div class="col-sm-12 visible-sm visible-xs">
        <br>
        <a href="{{ url('/') }}" class="btn btn-default">Back To Planner</a>
        <br>
        <br>
    </div>
    <div class="col-sm-10">
        <h3>Prices for ingredients</h3>
        <form method="POST" action="{{ route('prices.update') }}">
            {!! method_field('PUT') !!}
            {!! csrf_field() !!}
            <h4>Ingredients</h4>
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Price per unit</th>
                </tr>
                </thead>
                <tbody>
                @foreach($ingredients as $ingredient)
                    <tr>
                        <td>{{ $ingredient->name}}</td>
                        <td>
                            <input type="number"
                                   class="form-control"
                                   step=".01"
                                   name="ingredients[{{ $ingredient->id }}]"
                                   value="{{ $ingredient->price_per_unit }}"
                            ></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>

    </div>


</div>
@endsection
