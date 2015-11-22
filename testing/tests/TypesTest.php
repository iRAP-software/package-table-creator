<?php

/* 
 * This test will ensure that we can create a table with each of our defined types.
 */

namespace iRAP\TableCreator;

class TypesTest extends AbstractTest
{
    protected function test(mysqli $mysqli) 
    {
        $tableCreator = new TableCreator($mysqli, "test_table");
        
        $fields = array(
            DatabaseField::createInt('id', 11, $autoInc=true),
            DatabaseField::createBool('bool_field'),
            DatabaseField::createChar('char_field', 3),
            DatabaseField::createDate('date_field'),
            DatabaseField::createDecimal('decimal_field', 3, 4),
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
        $tableCreator->run();
        
        $query = "SHOW CREATE TABLE `test_table`";
        /* @var $result \mysqli_result */
        $result = $mysqli->query($query);
        $row = $result->fetch_array();
        $createTableString = $row[0];
        var_dump($createTableString);
    }
}
