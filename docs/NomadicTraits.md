# Nomadic Traits 
* Use Case: Reuse common functionality that your migrations tend to use.
   
## Usage 
Open up the nomadic.php and add your custom traits
```
return [
    'traits' => [
        \My\Custom\Trait::class
    ],
];
```
Now when you create migrations, you should see your stubbed migration using your custom traits.