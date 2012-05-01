PHP Coding Standards
---------------------------

General
########
For files that contain only PHP code, the closing tag ("?>") is never permitted. It is not required by PHP, and omitting it prevents the accidental injection of trailing white space into the response.

Indentation
############
Indentation should consist of four spaces. Tabs are not allowed. 

Maximum Line Length
#####################
The maximimum line length is eighty characters. In rare situations like using heredoc statements, the character length may exceed eighty characters. Please note, you can exceed eighty characters in a line only in exceptional situations.

Line Termination
##################
Line termination follows the UNIX text file convention. Lines must end with a single linefeed (LF) character. Linefeed characters are represented as ordinal 10 or hexadecimal 0x0A.

**Note**: Do not use carriage returns (CR) as is the convention in Apple OS' (0x0D) or the carriage return - linefeed combination (CRLF) as is standard for the Windows OS (0x0D, 0x0A). 

Naming Conventions
==========================

Classes
########################
Names of the classes directly map to the directories in which they are stored.

Class names may only contain alphanumeric characters. Numbers are permitted in class names but are discouraged in most cases. Underscores are only permitted in place of the path separator. 

* The BV library is placed in library/BV. BV is now deprecated. The code belonging to Bizsense will be moved to relevant subdirectories in the application directory. Generic code will be moved to Bare Framework.
* The model classes are placed in application/modules/default/models
* The prefix "Core" is used for all model classes


For example, the filename "application/modules/default/models/Lead/Index.php" must map to the class name "Core_Model_Lead_Index".

If a class name is comprised of more than one word, the first letter of each new word must be capitalized. Successive capitalized letters are not allowed, e.g. a class "URLAccess.php" is not allowed while "UrlAccess" is acceptable.

These conventions define a pseudo namespace mechanism for Bizsense. Bizsense will adopt the PHP 5.3 namespace feature when we use Zend Framework 2.0.

See the class names in the models and controllers directories for examples of this class name convention. 


Abstract Classes
###################
In general, abstract classes follow the same conventions as classes, with one additional rule: abstract class names must end in the term, "Abstract", and that term must not be preceded by an underscore. As an example, Core_Model_Abstract is considered an invalid name, but Core_Model_ModelAbstract is a valid name. 
Interfaces
###############
In general, interfaces follow the same conventions as classes, with one additional rule: interface names may optionally end in the term, "Interface", but that term must not be preceded by an underscore. As an example, Core_Model_Observable_Interface is considered an invalid name, but Core_Model_ObservableInterface or Core_Model_Observable_ObservableInterface would be valid names.

While this rule is not required, it is strongly recommended, as it provides a good visual cue to developers as to which files contain interfaces rather than classes. 


Filenames
###############

Any file that contains PHP code should end with the extension ".php", with the notable exception of view scripts. The following examples show acceptable filenames for Bizsense classes: 

* application/modules/default/models/Acl.php
* application/modules/default/models/SalesStage.php 
* application/modules/default/models/Newsletter/Subscriber.php

File names must map to class names as described above. 

For all other files, only alphanumeric characters, underscores, and the dash character ("-") are permitted. Spaces are strictly prohibited.

Functions and Methods
########################
Function names may only contain alphanumeric characters. Underscores are not permitted. Numbers are permitted in function names but are discouraged in most cases.

Function names must always start with a lowercase letter. When a function name consists of more than one word, the first letter of each new word must be capitalized. This is commonly called "camelCase" formatting.

Verbosity is generally encouraged. Function names should be as verbose as is practical to fully describe their purpose and behavior.

These are examples of acceptable names for functions: 

* filterInput()
* getElementById()
* widgetFactory()

For object oriented programming, accessors for instance or static variables should always be prefixed with "get" or "set". In implementing design patterns, such as the singleton or factory patterns, the name of the method should contain the pattern name where practical to more thoroughly describe behavior.

For methods on objects that are declared with the "private" or "protected" modifier, the first character of the method name must be an underscore. This is the only acceptable application of an underscore in a method name. Methods declared "public" should never contain an underscore.

Functions in the global scope (a.k.a "floating functions") are permitted but discouraged in most cases. Consider wrapping these functions in a static class. 

Variables
#############
Variable names may only contain alphanumeric characters. Underscores are not permitted. Numbers are permitted in variable names but are discouraged in most cases.

For instance variables that are declared with the "private" or "protected" modifier, the first character of the variable name must be a single underscore. This is the only acceptable application of an underscore in a variable name. Member variables declared "public" should never start with an underscore.

As with function names, variable names must always start with a lowercase letter and follow the "camelCaps" capitalization convention.

Verbosity is generally encouraged. Variables should always be as verbose as practical to describe the data that the developer intends to store in them. Terse variable names such as "$i" and "$n" are discouraged for all but the smallest loop contexts. If a loop contains more than 20 lines of code, the index variables should have more descriptive names. 

Constants
###############
Constants may contain both alphanumeric characters and underscores. Numbers are permitted in constant names.

All letters used in a constant name must be capitalized, while all words in a constant name must be separated by underscore characters.

For example, EMBED_SUPPRESS_EMBED_EXCEPTION is permitted but EMBED_SUPPRESSEMBEDEXCEPTION is not.

Constants must be defined as class members with the "const" modifier. Defining constants in the global scope with the "define" function is permitted but strongly discouraged. 

PHP Code Demarcation
########################
PHP code must always be delimited by the full-form, standard PHP tags::

    <?php
    >?

Short tags are never allowed. For files containing only PHP code, the closing tag must always be omitted.

Strings
==========================

String Literals
################
When a string is literal, containing no variable substitutions, the apostrophe or single quote should always be used to demarcate the string::

    <?php
        $a = 'Example String';
    ?>

String Literals Containing Apostrophes
#######################################
When a literal string itself contains apostrophes, it is permitted to demarcate the string with quotation marks or double quotes. This is especially useful for SQL statements::

    <?php
      $sql = "SELECT `id`, `name` from `people` "
           . "WHERE `name`='Fred' OR `name`='Susan'";
    ?>

This syntax is preferred over escaping apostrophes as it is much easier to read. 

Variable Substitution
##########################
Variable substitution is permitted using either of these forms::

    <?php
        $greeting = "Hello $name, welcome back!";
        $greeting = "Hello {$name}, welcome back!";
    ?>
 
For consistency, this form is not permitted::

    <?php
        $greeting = "Hello ${name}, welcome back!";
    ?>

String Concatenation
##########################
Strings must be concatenated using the "." operator. A space must always be added before and after the "." operator to improve readability::

    <?php
        $company = 'Acme' . ' Widgets';
    ?> 

When concatenating strings with the "." operator, it is encouraged to break the statement into multiple lines to improve readability. In these cases, each successive line should be padded with white space such that the "."; operator is aligned under the "=" operator::
    
    <?php
      $sql = "SELECT `id`, `name` FROM `people` "
           . "WHERE `name` = 'Susan' "
           . "ORDER BY `name` ASC ";
    ?> 

Arrays
==========================

Numerically Indexed Arrays
################################
Negative numbers are not permitted as indices.

An indexed array may start with any non-negative number, however all base indices besides 0 are discouraged.

When declaring indexed arrays with the Array function, a trailing space must be added after each comma delimiter to improve readability::

    <?php
        $sampleArray = array(1, 2, 3, 'Acme', 'Widgets');
    ?>

It is permitted to declare multi-line indexed arrays using the "array" construct. In this case, each successive line must be padded with spaces such that beginning of each line is aligned::

    <?php
      $sampleArray = array(1, 2, 3, 'Acme', 'Widgets',
                           $a, $b, $c,
                           56.44, $d, 500);
    ?> 

Alternately, the initial array item may begin on the following line. If so, it should be padded at one indentation level greater than the line containing the array declaration, and all successive lines should have the same indentation; the closing paren should be on a line by itself at the same indentation level as the line containing the array declaration::
    
    <?php
      $sampleArray = array(
          1, 2, 3, 'Acme', 'Widgets',
          $a, $b, $c,
          56.44, $d, 500,
      );
    ?>

When using this latter declaration, we encourage using a trailing comma for the last item in the array; this minimizes the impact of adding new items on successive lines, and helps to ensure no parse errors occur due to a missing comma. 

Associative Arrays
#########################
When declaring associative arrays with the Array construct, breaking the statement into multiple lines is encouraged. In this case, each successive line must be padded with white space such that both the keys and the values are aligned::

    <?php
      $sampleArray = array('firstKey'  => 'firstValue',
                           'secondKey' => 'secondValue');
    ?> 

Alternately, the initial array item may begin on the following line. If so, it should be padded at one indentation level greater than the line containing the array declaration, and all successive lines should have the same indentation; the closing paren should be on a line by itself at the same indentation level as the line containing the array declaration. For readability, the various "=>" assignment operators should be padded such that they align::

    <?php
      $sampleArray = array(
          'firstKey'  => 'firstValue',
          'secondKey' => 'secondValue',
      );
    ?>

When using this latter declaration, we encourage using a trailing comma for the last item in the array; this minimizes the impact of adding new items on successive lines, and helps to ensure no parse errors occur due to a missing comma. 

Classes
==========================

Class Declaration
#######################
Classes must be named according to Bizsense's naming conventions.

The brace should always be written on the line underneath the class name.

Every class must have a documentation block that conforms to the PHPDocumentor standard.

All code in a class must be indented with four spaces.

Only one class is permitted in each PHP file.

Placing additional code in class files is permitted but discouraged. In such files, two blank lines must separate the class from any additional PHP code in the class file.

The following is an example of an acceptable class declaration::

    <?php

        /**
        * Documentation Block Here
        */
        class SampleClass
        {
            // all contents of class
            // must be indented four spaces
        }
    ?>

Classes that extend other classes or which implement interfaces should declare their dependencies on the same line when possible::
    
    <?php
        class SampleClass extends FooAbstract implements BarInterface
        {
        }
    ?>

If as a result of such declarations, the line length exceeds the maximum line length, break the line before the "extends" and/or "implements" keywords, and pad those lines by one indentation level::

    <?php
        class SampleClass
            extends FooAbstract
            implements BarInterface
        {
        } 

    ?>

If the class implements multiple interfaces and the declaration exceeds the maximum line length, break after each comma separating the interfaces, and indent the interface names such that they align::

    <?php
        class SampleClass
            implements BarInterface,
                       BazInterface
        {
        }
    ?>

Class Member Variables
#########################
Member variables must be named according to Bizsense's variable naming conventions.

Any variables declared in a class must be listed at the top of the class, above the declaration of any methods.

The var construct is not permitted. Member variables always declare their visibility by using one of the private, protected, or public modifiers. Giving access to member variables directly by declaring them as public is permitted but discouraged in favor of accessor methods (set & get). 

Functions and Methods
==========================

Function and Method Declaration
####################################

Functions must be named according to Bizsense's function naming conventions.

Methods inside classes must always declare their visibility by using one of the private, protected, or public modifiers.

As with classes, the brace should always be written on the line underneath the function name. Space between the function name and the opening parenthesis for the arguments is not permitted.

Functions in the global scope are strongly discouraged.

The following is an example of an acceptable function declaration in a class::

    <?php
        /**
        * Documentation Block Here
        */
        class Foo
        {
            /**
            * Documentation Block Here
            */
            public function bar()
            {
                // all contents of function
                // must be indented four spaces
            }
        }
    ?>

In cases where the argument list exceeds the maximum line length, you may introduce line breaks. Additional arguments to the function or method must be indented one additional level beyond the function or method declaration. A line break should then occur before the closing argument paren, which should then be placed on the same line as the opening brace of the function or method with one space separating the two, and at the same indentation level as the function or method declaration. The following is an example of one such situation::

    <?php
        /**
        * Documentation Block Here
        */
        class Foo
        {
            /**
            * Documentation Block Here
            */
            public function bar($arg1, $arg2, $arg3,
              $arg4, $arg5, $arg6
            ) {
                // all contents of function
                // must be indented four spaces
            }
      }
    ?>

Pass-by-reference is the only parameter passing mechanism permitted in a method declaration. The following snippet is incorrect::
    
    <?php
        /**
        * Documentation Block Here
        */
        class Foo
        {
            /**
            * Do not use this style of passing parameters
            */
            public function bar(&$baz)
            {}
        }
    ?>

Call-time pass-by-reference is strictly prohibited. 

The return value must not be enclosed in parentheses. This can hinder readability, in additional to breaking code if a method is later changed to return by reference::
    
    <?php
        /**
        * Documentation Block Here
        */
        class Foo
        {
            /**
            * WRONG
            */
            public function bar()
            {
                return($this->bar);
            }
       
            /**
            * RIGHT
            */
            public function bar()
            {
                return $this->bar;
            }
        }
    ?>
        
Function and Method Usage
##########################
Function arguments should be separated by a single trailing space after the comma delimiter. The following is an example of an acceptable invocation of a function that takes three arguments::

    <?php
        threeArguments(1, 2, 3);
    ?>

Call-time pass-by-reference is strictly prohibited. See the function declarations section for the proper way to pass function arguments by-reference.

In passing arrays as arguments to a function, the function call may include the "array" hint and may be split into multiple lines to improve readability. In such cases, the normal guidelines for writing arrays still apply::

    <?php
        threeArguments(array(1, 2, 3), 2, 3);
       
        threeArguments(array(1, 2, 3, 'Binary', 'Vibes',
                             $a, $b, $c,
                             56.44, $d, 500), 2, 3);
       
        threeArguments(array(
            1, 2, 3, 'Zend', 'Studio',
            $a, $b, $c,
            56.44, $d, 500
        ), 2, 3);

    ?>

Control Statements
==========================

If/Else/Elseif
######################
Control statements based on the if and elseif constructs must have a single space before the opening parenthesis of the conditional and a single space after the closing parenthesis.

Within the conditional statements between the parentheses, operators must be separated by spaces for readability. Inner parentheses are encouraged to improve logical grouping for larger conditional expressions.

The opening brace is written on the same line as the conditional statement. The closing brace is always written on its own line. Any content within the braces must be indented using four spaces::
    
    <?php
        if ($a != 2) {
            $a = 2;
        }
    ?>

If the conditional statement causes the line length to exceed the maximum line length and has several clauses, you may break the conditional into multiple lines. In such a case, break the line prior to a logic operator, and pad the line such that it aligns under the first character of the conditional clause. The closing paren in the conditional will then be placed on a line with the opening brace, with one space separating the two, at an indentation level equivalent to the opening control statement::

    <?php
        if (($a == $b)
            && ($b == $c)
            || (Foo::CONST == $d)
        ) {
            $a = $d;
        }
    ?>

The intention of this latter declaration format is to prevent issues when adding or removing clauses from the conditional during later revisions.

For "if" statements that include "elseif" or "else", the formatting conventions are similar to the "if" construct. The following examples demonstrate proper formatting for "if" statements with "else" and/or "elseif" constructs::

    <?php
        if ($a != 2) {
            $a = 2;
        } else {
            $a = 7;
        }
       
        if ($a != 2) {
            $a = 2;
        } elseif ($a == 3) {
            $a = 4;
        } else {
            $a = 7;
        }
       
        if (($a == $b)
            && ($b == $c)
            || (Foo::CONST == $d)
        ) {
            $a = $d;
        } elseif (($a != $b)
            || ($b != $c)
        ) {
            $a = $c;
            $a = $b;
        }
    ?>


PHP allows statements to be written without braces in some circumstances. This coding standard makes no differentiation- all "if", "elseif" or "else" statements must use braces. 


Switch
##########
Control statements written with the "switch" statement must have a single space before the opening parenthesis of the conditional statement and after the closing parenthesis.

All content within the "switch" statement must be indented using four spaces. Content under each "case" statement must be indented using an additional four spaces::

    <?php
        switch ($numPeople) {
            case 1:
                break;
       
            case 2:
                break;
       
            default:
            break;
        }
    ?>



The construct default should never be omitted from a switch statement. 

It is sometimes useful to write a case statement which falls through to the next case by not including a break or return within that case. To distinguish these cases from bugs, any case statement where break or return are omitted should contain a comment indicating that the break was intentionally omitted. 

Inline Documentation
==========================

Documentation Format
#########################
All documentation blocks ("docblocks") must be compatible with the phpDocumentor format. Describing the phpDocumentor format is beyond the scope of this document. For more information, visit: http://phpdoc.org/

All class files must contain a "file-level" docblock at the top of each file and a "class-level" docblock immediately above each class. Examples of such docblocks can be found below.
Files

Every file that contains PHP code must have a docblock at the top of the file that contains these phpDocumentor tags at a minimum::

    <?php
      /**
      * Short description for file
      *
      * Long description for file (if any)...
      *
      * LICENSE: Some license information
      *
      * @category   Bizsense
      * @package    Bizsense
      * @subpackage Mymodule
      * @copyright  Copyright (c) 2011 Sudheera Satyanarayana, Bizsense contributors, et all
      * @license    New BSD
      * @version    $Id:$
      * @link       http://code.google.com/p/bizsensebms/
      * @since      File available since Release 0.3.2
      */
    ?>

The @category annotation must have a value of "Zend".

The @package annotation must be assigned, and should be equivalent to the component name of the class contained in the file; typically, this will only have two segments, the "Zend" prefix, and the component name.

The @subpackage annotation is optional. If provided, it should be the subcomponent name, minus the class prefix. In the example above, the assumption is that the class in the file is either "Zend_Magic_Wand", or uses that classname as part of its prefix. 


Classes
#########

Every class must have a docblock that contains these phpDocumentor tags at a minimum::

    <?php
      /**
      * Short description for class
      *
      * Long description for class (if any)...
      *
      * @category   Zend
      * @package    Zend_Magic
      * @subpackage Wand
      * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
      * @license    http://framework.zend.com/license   BSD License
      * @version    Release: @package_version@
      * @link       http://framework.zend.com/package/PackageName
      * @since      Class available since Release 1.5.0
      * @deprecated Class deprecated in Release 2.0.0
      */
    ?>

The @category annotation must have a value of "Zend".

The @package annotation must be assigned, and should be equivalent to the component to which the class belongs; typically, this will only have two segments, the "Zend" prefix, and the component name.

The @subpackage annotation is optional. If provided, it should be the subcomponent name, minus the class prefix. In the example above, the assumption is that the class described is either "Zend_Magic_Wand", or uses that classname as part of its prefix. 


Functions
##############

Every function, including object methods, must have a docblock that contains at a minimum:

    *

      A description of the function
    *

      All of the arguments
    *

      All of the possible return values

It is not necessary to use the "@access" tag because the access level is already known from the "public", "private", or "protected" modifier used to declare the function.

If a function or method may throw an exception, use @throws for all known exception classes::

    <?php
        @throws exceptionclass [description]
    ?>





