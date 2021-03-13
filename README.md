# WebDev2Assignment
Advanced Topics in Web Development 2 Assignment

## Goals
- Learn to model, clense & normalize substantial real-world big data (188 mb+);
- Languages invloves XML, XSD, XPath, PHP, Parsing, DOM, JavaScript adn JSON
- Learn and use the Markdown markup syntax

## Task 1: Cleanse and Refactor the Data (20 marks)
- Breakup the large original file air-quality-data-2004-2019.csv (188mb) into smaller CSV files each with readings of one specific station

## Task 2: Data Transformation, Normalisation & XML Validation (20 marks)
- Write a PHP script named normalize-to-xml.php to transform small CSV files from Task 1 into equivalent XML files
- Design and implement a XSD Schema named air-quality.xsd to validate the XML files generated.
- Marks depends on the overall design, structure and strictness of the schema

## Task 3: Chart Visualisation (20 marks)
- Google Chart API or equivalent can be used to implement this part
### a. A scatter Chart
- A scatter chart to show a years worth of data (averaged by month) from a specific station for Carbon Monoxide (NO) at a certain time of day - say 08.00 hours.

### a. A Line Chart
- A line chart showing levels in any 24 hour period on any day (user selectable) for any of the six stations (user selectable) for any of the major pollutants (nox, no, no2) in the date range downloaded.