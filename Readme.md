# Fluid Signature

Define expected variables of fluid templates.

> **Warning**: 
> Fluid has a [similiar functionality](https://github.com/NamelessCoder/Fluid/blob/master/src/ViewHelpers/ParameterViewHelper.php) in version 3.x and plans to have it also in 2.x. This solution is not compatible with what might become available in TYPO3 soon. If you want to migrate to the native functionality later it will be manual labor.

## Why?

Fluid often silently fails when you forget to pass a variable to a template, because any undefined variable will just evaluate as null.

````
<f:render section="hello" arguments="{firstName: 'Kasper'}"/>

<f:section name="hello">
Hello {firstName} {lastName}!
</f:section>
````

=> Hello Kasper !

On the other hand fluid also doesn't care if you pass variables to a template that aren't used.

````
<f:render section="hello" arguments="{firstName: 'Kasper', lastName: 'Skårhøj'}"/>

<f:section name="hello">
Hello {firstName}!
</f:section>
````

=> Hello Kasper!

That means passing too many or too few variables or mistyping their name is a common source of error.

## Solution

By using an `<f:signature />` tag you can define the expected variables at a certain point in your fluid code.

````
<f:section name="hello">
    <f:signature required="firstname, lastname" allowed="country"/>
    Hello {firstName} {lastName}{f:if(condition: country, then: ' from {country}')}!
</f:section>

<f:render section="hello" arguments="{firstName: 'Kasper'}"/>
=> ❌ Exception: Required fluid variable lastName is not available

<f:render section="hello" arguments="{firstName: 'Kasper', lastName: 'Skårhøj', profession: 'Engineer'}"/>
=> ❌ Exception: Variable profession was provided but not allowed

<f:render section="hello" arguments="{firstName: 'Kasper', lastName: 'Skårhøj'}"/>
=> ✅ Hello Kasper Skårhøj!

<f:render section="hello" arguments="{firstName: 'Kasper', lastName: 'Skårhøj', country: 'Denmark'}"/>
=> ✅ Hello Kasper Skårhøj from Denmark!
````

## Attributes

`required`: Comma separated list of variables. If given, an exception is thrown if any of the variables in not defined in the current variable context.

`allowed`: Comma separated list of variables. If given the current variable context is checked for any variables that are not in `required` or `allowed` and an exception is thrown if a variable is found. `allowed=""` means only the `required` variables are allowed.

`defaultValues`: Associative array. `defaultValues="{country: 'Iceland'}"` means `country` will be set to `Iceland` if it is not defined in the current variable context.

## Installation

`composer require smic/fluid-signature dev-main`

The package is tested with TYPO3 v10, but should be compatible from v8 to v11.

I'll accept bug reports for TYPO3 v10 and v11 and pull requests for earlier versions.
