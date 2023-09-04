# Laravel Resource Reducer from ShipSaaS

[![Build & Test (PHP 8.2 + Laravel 10+)](https://github.com/shipsaas/laravel-resource-reducer/actions/workflows/build.yml/badge.svg)](https://github.com/shipsaas/laravel-resource-reducer/actions/workflows/build.yml)
[![codecov](https://codecov.io/gh/shipsaas/laravel-resource-reducer/graph/badge.svg?token=alt8CdVUg1)](https://codecov.io/gh/shipsaas/laravel-resource-reducer)


Ever thinking about how to speed up your application by optimizing the response? 

Laravel Resource Reducer helps you to optimize every API request by:

- Reduce the response's size, get what you need ⭐️
  - Defer execution and allow on-demand data
- Responses to consumers faster 🚀
  - No more BIG FAT JSON every item/request
- Computation only starts when requires, save CPU & memory 😎
- Built-in relationship access by using dot notation 👀
- Eager-loading on steroids (automated eager-loading, no more N+1 pain) 🔋

A simple yet super effective method to skyrocketing your API responding times 🥰

> If you know about GraphQL query, in order to query for data, we need to define which _fields_ we want to get
in our context.

## Supports
- Laravel 10+
- PHP 8.2+

### Compatibility
- Single Model ✅
- Collection of Models ✅
- Pagination ✅ (🟡 you need to use `Resource::collection` for the time being)

## Installation

```bash
composer require shipsaas/laravel-resource-reducer
```

## Usage

Laravel Resource Reducer is the SuperSet from Laravel Resource, thus we can use the Reducer just like 
the way we use normal Resource.

For detailed documentation & best practices, check out: [Reducer Documentation](https://reducer.shipsaas.tech)

### Resource Class

Simply migrate your `Resource` class by extending `ShipSaasReducer\Json\JsonReducerResource`, implement the
`definitions` method.

The migration is 1:1 migration, no breaking changes 😉.

```php
class UserResource extends JsonReducerResource
{
    public function definitions(Request $request): array
    {
        return [
            'id' => fn () => $this->id,
            'email' => fn () => $this->email,
            'created_at' => fn () => $this->created_at,
        ];
    }
}
```

Remember to wrap your accessor in a Closure/Callable. 
This ensures computation won't start on Runtime (wait for the right time 😉). 

**NOTE:** remember to remove the `toArray()` if you are migrating to `JsonReducerResource` 🥹, we handles magic there.

### Return the data

Same as today as how we are using Laravel Resource:

```php
// UserController@index
return UserResource::collection($users)->response();

// UserController@show
return (new UserResource($users->first()))->response();
```

### From API consumers

Use the query `_f` or `_fields`, Reducer supports both ways:

- `http://api/users?_f=id,name,role.name,created_at`
- `http://api/users?_fields[]=id,_fields[]=email`

## Testing

Run `composer test` 😆

Available Tests:

- Unit Testing
- Feature Testing

## Contributors
- Seth Phat

## Contributions & Support the Project

Feel free to submit any PR, please follow PSR-1/PSR-12 coding conventions and testing is a must.

If this package is helpful, please give it a ⭐️⭐️⭐️. Thank you!

## License
MIT License
