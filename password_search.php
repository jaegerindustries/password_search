<?php

/*
 *
 *  Password Search
 *
 *  Copyright (c) 2015-2016, Philippe Paquet
 *  All rights reserved.
 *  
 *  Redistribution and use in source and binary forms, with or without modification,
 *  are permitted provided that the following conditions are met:
 *  
 *  1. Redistributions of source code must retain the above copyright notice, this
 *     list of conditions and the following disclaimer.
 *  
 *  2. Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *  
 *  3. Neither the name of the copyright holder nor the names of its contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *  
 *  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 *  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 *  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 *  IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 *  INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
 *  NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 *  PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 *  WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 *  ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 *  POSSIBILITY OF SUCH DAMAGE.
 *
 */

$array_extension_regex = array(
	// Cryptographic key
	array("/asc/i", "Potential cryptographic key bundle"),
	array("/p12/i", "Potential cryptographic key bundle"),
	array("/pem/i", "Potential cryptographic private key"),
	array("/pfx/i", "Potential cryptographic key bundle"),
	array("/pkcs12/i", "Potential cryptographic key bundle"),
	// Password database file
	array("/agilekeychain/i", "1Password database file"),
	array("/kdb/i", "KeePass database file"),
	array("/kdbx/i", "KeePassdatabase file"),
	array("/keychain/i", "Apple Keychain database file"),
	array("/kwallet/i", "KDE Wallet Manager database file"),
	array("/pwm/i", "PwManager database file"),
	// VPN configuration files
	array("/ovpn/i", "OpenVPN configuration file"),
	array("/tblk/i", "Tunnelblick configuration file"),
);

$array_regex = array(
	"/'\S*KEYWORD'\s*?,\s*?'\S+?'/i",
	"/'\S*KEYWORD'\s*?,\s*?\"\S+?\"/i",
	"/'\S*KEYWORD'\s*?:\s*?'\S+?'/i",
	"/'\S*KEYWORD'\s*?:\s*?\"\S+?\"/i",
	"/'\S*KEYWORD'\s*?=>\s*?'\S+?'/i",
	"/'\S*KEYWORD'\s*?=>\s*?\"\S+?\"/i",
	"/\"\S*KEYWORD\"\s*?,\s*?'\S+?'/i",
	"/\"\S*KEYWORD\"\s*?,\s*?\"\S+?\"/i",
	"/\"\S*KEYWORD\"\s*?:\s*?'\S+?'/i",
	"/\"\S*KEYWORD\"\s*?:\s*?\"\S+?\"/i",
	"/\"\S*KEYWORD\"\s*?=>\s*?'\S+?'/i",
	"/\"\S*KEYWORD\"\s*?=>\s*?\"\S+?\"/i",
	"/\[\s*?'\S*KEYWORD'\s*?\]\s*?=\s*?'\S+?'/i",
	"/\[\s*?'\S*KEYWORD'\s*?\]\s*?=\s*?\"\S+?\"/i",
	"/\[\s*?\"\S*KEYWORD\"\s*?\]\s*?=\s*?'\S+?'/i",
	"/\[\s*?\"\S*KEYWORD\"\s*?\]\s*?=\s*?\"\S+?\"/i",
	"/\$\S*KEYWORD\s*?=\s*?'\S+?'/i",
	"/\$\S*KEYWORD\s*?=\s*?\"\S+?\"/i",
	"/<\s*?\S*KEYWORD\s*?>\s*?\S+\s*?<\s*?\/\S*KEYWORD\s*?>/i",
	"/<\s*?string\s*?name\s*?=\s*?\"\S*KEYWORD\"\s*?>\s*?\S+\s*?<\s*?\/string\s*?>/i",
	"/AKIAI[0-9a-z]+?\s/i",
	"/\S*KEYWORD:encrypt\s*?\(\s*?\"\S+\"\s*?\)/i",
	"/\S*KEYWORD\s*?:\s*?'\S+?'/i",
	"/\S*KEYWORD\s*?:\s*?\"\S+?\"/i",
	"/\S*KEYWORD\s*?=\s*?'\S+?'/i",
	"/\S*KEYWORD\s*?=\s*?\"\S+?\"/i",
	"/\S*KEYWORD\s*?=\s*?\S+\s/i",
	"/set_\S*KEYWORD\s*?\(\s*?'\S+'\s*?\)/i",
	"/set_\S*KEYWORD\s*?\(\s*?\"\S+\"\s*?\)/i",
	"/set\S*KEYWORD\s*?\(\s*?'\S+'\s*?\)/i",
	"/set\S*KEYWORD\s*?\(\s*?\"\S+\"\s*?\)/i",
	"/System.Net.NetworkCredential\s*?\(\s*?\"(\S+?)\"\s*?,\s*?\"(\S+?)\"\s*?\)/i",
);

$array_keywords = array(
	"key",
	"pass",
	"passphrase",
	"password",
	"passwd",
	"pwd",
	"secret",
	"token",
);

$array_extensions = array(
	"c",
	"cpp",
	"cs",
	"h",
	"hpp",
	"htm",
	"html",
	"ini",
	"java",
	"js",
	"json",
	"php",
	"pl",
	"properties",
	"py",
	"rb",
	"vb",
	"xml",
	"yml",
);





//
//
//  make_path
//
//

function make_path($path)
{
	$path = explode(DIRECTORY_SEPARATOR, realpath($path));
	$rebuild = '';
	foreach($path as $p) {
		$rebuild .= "/$p";
		if(FALSE == is_dir($rebuild)) {
			mkdir($rebuild);
		}
	}
}





//
//
//  list_files
//
//

function list_files($dir, $follow_links)
{
	$result = array();
	if(TRUE == is_dir($dir)) {
		$files = array_diff(scandir($dir), array('.', '..'));
		foreach ($files as $file) {
			if (TRUE == is_dir($dir . DIRECTORY_SEPARATOR  . $file)) {
				$result = array_merge(list_files($dir. DIRECTORY_SEPARATOR . $file, $follow_links), $result);
			} else {
				if (TRUE == is_file($dir . DIRECTORY_SEPARATOR . $file)) {
					$result[] = $dir . DIRECTORY_SEPARATOR . $file;
				} else {
					if (TRUE == is_link($dir . DIRECTORY_SEPARATOR  . $file)) {
						if (TRUE == $follow_links) {
							if (TRUE == is_file(readlink($dir . DIRECTORY_SEPARATOR . $file))) {
								$result[] = readlink($dir . DIRECTORY_SEPARATOR . $file);
							} else {
								if (TRUE == is_dir(readlink($dir . DIRECTORY_SEPARATOR . $file))) {
									$result = array_merge(list_files(readlink($dir . DIRECTORY_SEPARATOR . $file), $follow_links), $result);
								}
							}
						}
					}
				}
			}
		}
	}
	return $result;
}





// Banner
echo "\r\n";
echo "Password Search v3.0\r\n";
echo "\r\n";
echo "Copyright 2015-2016 Philippe Paquet, All Rights Reserved.\r\n";
echo "\r\n";

// Parse arguments
if (3 == $argc) {
	$path_search = rtrim($argv[1], '/');
	$filename_report = $argv[2];
} else {
	echo "password_search <directory> <report>\r\n";
	echo "\r\n";
	echo "<directory>  directory to search for password\r\n";
	echo "<report>     path to the report file (csv format)\r\n";
	echo "\r\n";
	exit(-1);
}

// Create report path and file
$path_parts = pathinfo($filename_report);
make_path($path_parts['dirname']);
$handle_report = fopen($filename_report, 'w');
if ($handle_report === FALSE) {
	echo "Cannot open $filename_report\r\n";
	echo "\r\n";
	exit(-2);
} else {
	fputcsv($handle_report, array("File", "Line", "Password"), ',');
}

// Expand the regular expression array and remove duplicates
$array_regex_expanded = array();
foreach ($array_regex as $regex) {
	foreach ($array_keywords as $keyword) {
		array_push($array_regex_expanded, str_replace('KEYWORD', $keyword, $regex));
	}
}
$array_regex_expanded = array_unique($array_regex_expanded);

// Initialize statistics
$stats_processed_files_number = 0;
$stats_analyzed_files_number = 0;
$stats_password_number = 0;
$stats_password_files_number = 0;

// Go through the files
$files = list_files($path_search, FALSE);
foreach ($files as $file) {
	$path_parts = pathinfo($file);
	if ((TRUE == array_key_exists('extension', $path_parts)) && (realpath($file) != realpath($filename_report))) {
		$file_processed = FALSE;
		foreach ($array_extension_regex as $regex) {
			if (1 == preg_match($regex[0], $path_parts['extension'])) {
				echo '*';
				fputcsv($handle_report, array($file, 'n/a', $regex[1]), ',');
				$stats_password_files_number++;
				$file_processed = TRUE;
			}
		}
		if ((FALSE == $file_processed) && (TRUE == in_array($path_parts['extension'], $array_extensions))) {
			echo '.';
			$stats_line_number = 1;
			$handle = fopen($file, 'r');
			if (FALSE !== $handle) {
				while (FALSE !== ($line = fgets($handle, 8192))) {
					$line = trim($line);
					foreach ($array_regex_expanded as $regex) {
						preg_match_all($regex, $line, $matches, PREG_SET_ORDER);
						foreach ($matches as $match) {
							echo '*';
							fputcsv($handle_report, array($file, $stats_line_number, $match[0]), ',');
							$stats_password_number++;
						}
					}
					$stats_line_number++;
				}
				fclose($handle);
			} else {
				echo "\r\n";
				echo "Cannot open $file\r\n";
			}
			$stats_analyzed_files_number++;
		}		
		$stats_processed_files_number++;
	}
}

// Close report file
fclose($handle_report);

// Display statistics
echo "\r\n";
echo "\r\n";
echo "$stats_processed_files_number files processed\r\n";
echo "$stats_analyzed_files_number files analyzed\r\n";
echo "\r\n";
echo "$stats_password_files_number potential credential files found\r\n";
echo "$stats_password_number potential passwords found\r\n";
echo "\r\n";
