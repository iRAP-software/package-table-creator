<?php

/* 
 * This test will ensure that we can create a table with each of our defined types.
 */

namespace iRAP\TableCreator\Testing\Tests;

use iRAP\TableCreator\DatabaseField;
use iRAP\TableCreator\TableCreator;
use iRAP\TableCreator\Testing\AbstractTest;

class TypesTest extends AbstractTest
{
    protected function test(\mysqli $mysqli)  : void
    {
        $tableCreator = new TableCreator($mysqli, "test_table");
        
        $fields = array(
            DatabaseField::createInt(name: 'id', size: 11, autoInc: true),
            DatabaseField::createBool('bool_field'),
            DatabaseField::createVarchar('varchar_field', 255),
            DatabaseField::createChar('char_field', 3),
            DatabaseField::createDate('date_field'),
            DatabaseField::createDecimal('decimal_field', 4, 4),
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
        
        $query = "SHOW CREATE TABLE `test_table`";
        /* @var $result \mysqli_result */
        $result = $mysqli->query($query);
        $row = $result->fetch_array();
        $createTableString = $row[1];
        $this->m_passed = ($createTableString === $this->getExpectedTableString());

        if (!$this->m_passed)
        {
            $this->m_errorMessage = "Generated table did not match expected structure.";
        }
    }


    /**
     * Helper method to return the raw string expected from the database for the structure of the table
     * we are creating in this test.
     * @return string
     */
    private function getExpectedTableString() : string
    {
        return 'CREATE TABLE `test_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bool_field` tinyint(1) NOT NULL,
  `varchar_field` varchar(255) NOT NULL,
  `char_field` char(3) NOT NULL,
  `date_field` date NOT NULL,
  `decimal_field` decimal(4,4) NOT NULL,
  `geometry_field` geometry NOT NULL,
  `geometry_collection_field` geometrycollection NOT NULL,
  `line_string_field` linestring NOT NULL,
  `long_text_field` longtext NOT NULL,
  `multi_line_string_field` multilinestring NOT NULL,
  `multi_point_field` multipoint NOT NULL,
  `multi_polygon_field` multipolygon NOT NULL,
  `point_field` point NOT NULL,
  `polygon_field` polygon NOT NULL,
  `text_field` text NOT NULL,
  `timestamp_field` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci';

    }
}


