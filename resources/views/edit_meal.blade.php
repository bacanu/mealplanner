@extends('layouts.default')

@section('content')
    
<div class="container" id="app">
    <div class="col-sm-12 visible-sm visible-xs">
        <br>
        <a href="{{ url('planner') }}" class="btn btn-default">Back To Planner</a>
        <br>
        <br>
    </div>
    <div class="col-sm-10">
        <h3>{{ $meal->name }}</h3>
        <form method="POST" action="{{ route('meals.ingredients.store', $meal->id) }}">
            {!! method_field('POST') !!}
            {!! csrf_field() !!}
            <div class="form-group">
                <label for="name">Ingredient</label>
                <input type="text" class="form-control" name="name">
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="text" class="form-control" name="quantity">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <h4>Ingredients</h4>
        <table class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>Name</th>
                <th>Quantity</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($meal->ingredients as $ingredient)
                <tr>
                    <td>{{ $ingredient->name}}</td>
                    <td>{{ $ingredient->quantity }}</td>
                    <td>
                        <form method="POST" action="{{ route('meals.ingredients.destroy', [$meal->id, $ingredient->id]) }}">
                            {!! method_field('DELETE') !!}
                            {!! csrf_field() !!}
                            <button class="btn btn-danger" type="submit">&times;</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>


</div>
@endsection