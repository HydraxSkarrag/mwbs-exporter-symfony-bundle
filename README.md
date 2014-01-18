README
======

Symfony2 Bundle for the "MySQL Workbench Schema Exporter for Doctrine2 Annotations"


Setup
-----

Workbench files are saved in the Resources/workbench/*.mwb directory inside your bundle. 


Configuration
=============

Single schema
-------------

`schema_name` here refers to name of the Workbench file

    mwbs_exporter:
        schema:
            schema_name:
                bundle: YourBundle


Multiple schemas
----------------

`schema_name` here refers to name of the Workbench file

    mysql_workbench_schema_exporter:
        schema:
            schema1_name:
                bundle: YourBundle
            schema2_name:
                bundle: YourBundle
            schema3_name:
                bundle: YourBundle
                params:
                    bundleNamespace: "YourBundle"
                    repositoryNamespace: "YourBundle\\Entity"
                    entityNamespace: ""
                    useAnnotationPrefix: "ORM\\"
                    generateBaseClasses: true
                    useAutomaticRepository: true
                    skipPluralNameChecking: false
                    enhanceManyToManyDetection: true
                    filename: "%%entity%%.%%extension%%"
                    backupExistingFile: false
                    indentation: 4
                    quoteIdentifier: false

Execution
=========

To process the files execute the command in the terminal:

	app/console mwbs:export-entities

Exporter Options
----------------

### Exporter options

  * `bundleNamespace`

    Name of your Bundlenamespace

  * `repositoryNamespace`

    Name of your repositoryNamespace
    
  * `entityNamespace`

    Name of your entityNamespace
    
  * `useAnnotationPrefix`

    Doctrine annotation prefix. Default is `ORM\`.
    
  * `generateBaseClasses`

    Generate Entity Base Classes
    
  * `useAutomaticRepository`

    See above.
    
  * `skipPluralNameChecking`

    Skip checking the plural name of model and leave as is, useful for non English table names. Default is `false`.
    
  * `enhanceManyToManyDetection`

    If enabled, many to many relations between tables will be added to generated code. Default is `true`.
    
  * `filename`

    The output filename format, use the following tag `%schema%`, `%table%`, `%entity%`, and `%extension%` to allow
    the filename to be replaced with contextual data. Default is `%entity%.%extension%`.

  * `backupExistingFile`

    If target already exists create a backup before replacing the content. Default is `true`.
  
  * `indentation`

    The indentation size for generated code.  

  * `quoteIdentifier`

    If this option is enabled, all table names and column names will be quoted using backtick (`` ` ``). Usefull when the table name or column name contains reserved word. Default is `false`.
