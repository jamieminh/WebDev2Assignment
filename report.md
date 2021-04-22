# Task 5

## Parsing methods and tools

There are two main types of document parsing, the first one is Document Object Model oriented parsers, also known as DOM, the second are event-based, or stream-oriented parsers, including Simple API for XML, or SAX and another slightly more efficient method called StAX (Streaming API for XML).

1. A DOM parser parse the entire document only once into memory and create a tree structure of it, we can easily and quickly traverse though branches to get what we need after it has finished parsing [[1]]. Basics operations including create, read, update, and delete (CRUD) are available with this method. 
2. A SAX parser, on the other hand, is trigger-based, data is only parsed when it encounters an opening tag or closing tag, a comment and so on. The document is read from top to bottom and as such does not offer backward navigation. It is a push parsing method, where the client (or handler) has no control over the operation, and the parser will return all data even the client is not ready to use it at that time [[2]]. SAX parsers can only perform read operations.
3. A StAX parser is similar to a SAX parser the way it differs from a DOM parser, but it is a pull parsing model, where the handler has controls over the parser to get data when needed, and filter out unnecessary data. Unlike SAX, StAX parsers can perform write functions as well [[2]].


The PHP language offers DOM and SimpleXML for tree-based parsing, and XMLReader and XML Expat Parser for stream parsing[[3]]. The differences between each tool are shown below:

| DOM | SimpleXML | 
| ------ | ------ |
| use **libxml2** toolkit | use **libxml2** toolkit|
| more complex | easy to use |
| powerful CRUD operations | focused on read and write, less focus on update or delete|
[[4]]

| XMLReader | XML Expat Parser
| ------ | ------ |
| pull parser | push parser |
| traverse by creating a loop and move the cursor forward | must define callback functions that will be triggered by events (start or end of element, etc)
 

Due to its behaviour, a DOM parser is more suitable for small data files, and/or applications that requires considerable tree traversing back and forth. For bigger documents, it would be extremely time-consuming and memory-consuming since all content must be loaded into memory first. In these cases, a SAX or StAX parser is a much more ideal option, especially when a document does not have a complex structure. Additionally, according to Oracle [[2]], StAX parsers are particularly useful for data binding, SOAP message processing, Virtual data sources, parsing specific XML vocabularies and pipelined XML processing.

For comparison, map visualisation in task 4 requires reading and processing data from all 18 XML files, it takes around 9s on average for the page to load and display using a DOM parser, but only 4s when using a stream parser.


## Code structure 
Note: the "air-quality-data-2004-2019.csv" file should be placed in the top level (see below).
Github link: https://github.com/jamieminh/WebDev2Assignment
```
atwd2
├── <<air-quality-data-2004-2019.csv>>
├── extract-to-csv.php
├── normalize-to-xml.php
├── air-quality.xsd
│ 
├── <<csv and xml files produced from task 1 and 2>>
│
├── charts
│   ├── task3Styles.css
│   ├── task3a
│   │    ├── form.php
│   │    └── index.php
│   └── task3b
│        ├── form.php
│        └── index.php
│
├── maps
│   └── task4
│        ├── assets
│        │   └── <<map pin images based on colors>>
│        ├── form.php
│        ├── index.php
│        └── style.css
│
└── report.md
```

## Further Improvements

Instead of having the graphs and map on separate sites, I would create an SPA (Single Page Application) using React. Any common figure should be cached in the react redux store so that the application can reuse it, the same goes for map visualisation where data generated for each combination of user selected year and pollution chemical should also be stored, so user would not have to wait long the next time they re-enter a combination.


[1]: https://docs.oracle.com/cd/E19575-01/819-3669/bnbdx/index.html
[2]: https://docs.oracle.com/cd/E17802_01/webservices/webservices/docs/1.6/tutorial/doc/SJSXP2.html
[3]: https://www.w3schools.com/php/php_xml_parsers.asp
[4]: https://stackoverflow.com/questions/4803063/whats-the-difference-between-phps-dom-and-simplexml-extensions


