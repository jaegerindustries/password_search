# Password Search

## Requirements

Password Search is written in PHP and will work on any system that support PHP including Microsoft Windows, Apple OS X and Linux. The only requirement is to have PHP installed.

## Usage

Password Search is invoked through the command line:

php password_search.php \<directory\> \<report\>

Where:
* \<directory\> is directory to search for passwords
* \<report\> is the path to the report file

Report are written in CSV (Comma Separated Values) format.  You can open them with a variety of software, including Microsoft Excel.

## How it works

Password search works by combining keywords and regular expressions.  You can check the presentation given at Password^15 for more information:
* https://github.com/jaegerindustries/passwords15
* https://www.youtube.com/watch?v=K7DtLzRDb8w

## Contributing

Bug reports and suggestions for improvements are most welcome.  Of particular interest are keywords, API key formats, functions names or file names indicating the presence of credentials.
