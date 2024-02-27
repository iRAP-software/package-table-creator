A package to simplify the management of MySQL tables.

## Example Usage
The example below demonstrates how one can use this package to easily create a new MySQL database
table:

```php
use iRAP\TableCreator\DatabaseField;
use iRAP\TableCreator\TableCreator;
use iRAP\TableCreator\Testing\AbstractTest;

function createTestTable(\mysqli $db)
{
  $tableCreator = new TableCreator($db, "test_table");
          
  $fields = array(
      DatabaseField::createInt('id', 11, true),
      DatabaseField::createBool('bool_field'),
      DatabaseField::createVarchar('varchar_field', 255),
      DatabaseField::createChar('char_field', 3),
      DatabaseField::createDate('date_field'),
      DatabaseField::createDecimal('decimal_field', 5, 4),
      DatabaseField::createGeometry('geometry_field'),
      DatabaseField::createGeometryCollection('geometry_collection_field'),
      DatabaseField::createLineString('line_string_field'),
      DatabaseField::createLongText('long_text_field'),
      DatabaseField::createMultiLineString('multi_line_string_field'),
      DatabaseField::createMultiPoint('multi_point_field'),
      DatabaseField::createMultiPolygon('multi_polygon_field'),
      DatabaseField::createPoint('point_field'),
      DatabaseField::createPolygon('polygon_field'),
      DatabaseField::createText('text_field'),
      DatabaseField::createTimestamp('timestamp_field')
  );
  
  $tableCreator->addFields($fields);
  $tableCreator->setPrimaryKey('id');
  $tableCreator->run();
}
```


## Testing

When testing, you may find it easier to just make use of Docker Compose to spin up a temporary 
database to test against like so:

```yaml
version: "3"

services:
  db:
    image: mariadb:latest
    container_name: db
    ports:
      - "3306:3306"
    volumes:
      - temp-mysql-data:/var/lib/mysql
    environment:
      - MARIADB_ROOT_PASSWORD=thisIsTheTestingPa55w0rd
      - MARIADB_DATABASE=testing

volumes:
  temp-mysql-data:
    driver: local
```

Then simply create the `Settings.php` file from the template with the relevant values, and execute
the tests with `php ./testing/main.php`.