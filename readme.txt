Website Scraper

Project Description
Website Scraper is a PHP-based web tool that allows users to fetch and locally replicate the frontend structure of any website. It supports extraction of HTML files, CSS stylesheets, JavaScript files, images, and fonts. The system provides an interactive interface to preview and download assets into a structured local directory.

Features:
Fetch any website using a URL
Extract HTML pages
Clone CSS stylesheets
Extract JavaScript files
Download images locally
Extract font files (eot, ttf, woff, woff2)
Organized directory structure
Hide and show assets dynamically
One-click asset generation

System Requirements:
PHP version 7.0 or higher
Apache Server
XAMPP recommended

Download XAMPP from:
https://www.apachefriends.org/

Installation Guide:

Step 1 Install XAMPP
Download and install XAMPP
Open XAMPP Control Panel
Start Apache
MySQL is optional

Step 2 Setup Project
Copy the project folder named website-scraper
Paste it into C:\xampp\htdocs\

Step 3 Run the Project
Open your browser
Go to http://localhost/website-scraper/
The main interface will load

How the System Works:

1 URL Input and Fetch
Enter any website URL in the input field
Click the Fetch button
The system retrieves HTML, CSS, JS, Images, and Fonts

2 Sessions Overview
After fetching, the system displays four main sessions

HTML Session
Displays all HTML file links
Click Generate button to save HTML locally

CSS Session
Displays all CSS files
Click Generate button to save CSS locally

JS Session
Displays all JavaScript files
Click Generate button to save JS locally

Image Session
Displays all image links
Click Generate button to download images locally

Top Control Buttons

Image Fetch Button
Fetches all images from HTML files
Stores them in /images/
Includes hide and show functionality
Appears only when HTML exists

CSS Clone Button
Extracts all CSS files
Stores them in /css/
Includes hide and show functionality
Active only when CSS exists

Fonts Extract Button
Extracts font files eot, ttf, woff, woff2
Stores them in /css/
Includes hide and show functionality
Depends on CSS availability

Error Handling
If you encounter the error Invalid URL or unable to fetch page
Use a VPN because some websites block automated requests

Directory Structure

Website-scraper
-docs
-system
--assets
---css
   -bootstrap.min.css
---js
   -jquery.js
-config
--config.txt
-inc
 -common.php
 -functions.php
 -repair-links.php
 -replicator-css.php
 -replicator-fonts.php
 -replicator-images.php
 -validator-css.php
 -validator-fonts.php
 -validator-html.php
 -themazine.com
   -css
   -images
   -js
 -about.html
 -blog.html
-index.html
 -index.php

Core Modules Explanation

functions.php
Contains helper functions for fetching content, parsing URLs, and file handling

replicator files
Responsible for downloading assets such as CSS, images, and fonts

validator files
Validates fetched data, ensures files exist, and prevents invalid downloads

repair-links.php
Fixes broken or relative URLs and converts them into absolute paths

Use Cases
Website cloning for learning
UI and UX analysis
Offline website storage
Theme extraction

Disclaimer
This tool is for educational purposes only
Do not use it to violate copyrights
Always respect website terms and policies

Author
Developed by Faizan Mahmood

Social Links
Upwork
https://www.upwork.com/freelancers/~01a6c5c9a4586953fe?mp_source=share

Fiverr
https://www.fiverr.com/s/R78ajKl

LinkedIn
https://www.linkedin.com/in/faizan-rajpoot-25970a249

WordPress
https://profiles.wordpress.org/faizanmahmood/

GitHub
https://github.com/faizan-mahmood01

Support
If you like this project
Star the repository
Fork it
Contribute improvements

License:

This project is licensed under the MIT License.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files, to deal in the software
without restriction, including without limitation the rights to use, copy,
modify, merge, publish, distribute, sublicense, and or sell copies of the
software, subject to the following conditions

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the software

The software is provided as is, without warranty of any kind, express or implied,
including but not limited to the warranties of merchantability, fitness for a
particular purpose and noninfringement. In no event shall the authors or
copyright holders be liable for any claim, damages or other liability
