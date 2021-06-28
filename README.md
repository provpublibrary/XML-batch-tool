## XML Batch Tool
This tool was created at Providence Public Library to generate item level XML files from a single CSV spreadsheet. 

### Prerequisites
MAMP
CSV file for metadata

### Required Metadata Fields
The XML Batch Tool can process basic items with one file per record, compound objects, or books. Currently, the tool can handle any file type for basic objects. For compound objects, the files must be the same format, and the same number of files per object. Books are generated from a series of JPG or PDF files.

The tool is somewhat strict as to formatting metadata elements. This [Google Sheet describes the required metadata elements and their use by PPL](https://docs.google.com/spreadsheets/d/1lykfa6cuMP7UauiZR7Qj1Q0kVVZU_gs2rDbOELLjDRE/edit#gid=0). 

### Use
- Export the spreadsheet as a CSV file
- Move the CSV file to the htdocs directory in the MAMP folder
- Open MAMP and start the servers
- Navigate to [http://localhost:8888/xml-batch-tool/](http://localhost:8888/xml-batch-tool/) in your browswer of choice, and follow the directions on the page.
	- Choose the CSV file you wish to convert
	- Select the type of batch: Basic, Compound, or Book
	- Fill out the Number of Images field **only** if you are running a compound batch, and the Book Title and Number of Book Pages fields **only** if you are running a book batch.
	- Click submit.
- The generated XML files will be output to a new folder within the htdocs/XML-batch-tool directory, named after the CSV file \[CSV filename\]\_ingest, with all derivative files \(including original CSV\) inside.

### Updating the Tool
This tool ignores CSV and XML files, and \_Input folders that are generated. 
