# Roll of Honour (ROH) — South African

A website to honour South African military personnel who died in service. This is a revival of a database and website originally created in the mid-1990s.

## Overview

The **ROH** folder contains a lightweight **PHP + SQLite + Bootstrap** web application that displays:

- Personal records (service number, rank, name, unit, date/cause of death, etc.)
- Commemoration details (cemetery, grave reference, map links)
- Associated images (integrated with the GrabImages VB.NET downloader)
- Statistical charts (by year, cause, regiment, country, etc.)
- Searchable and paginated lists

### Main Features

- **Home Page** (`index.php`) — Landing page with navigation and basic info
- **Person Detail** (`person.php?PersonNumber=...`) — Full profile with images, death details, and Google Maps embed
- **Lists** (`mainListPeople.php`, `listPeopleYear.php`, `listPeopleRegiment.php`, etc.) — Paginated and sortable views
- **Charts** (`chartYear.php`, `chartCauseOfDeath.php`, `chartRegiment.php`, …) — Visual statistics
- **Image Support** — Displays downloaded photos from the `DownLoadImage/` folder (populated by the companion VB tool)

## Technology Stack

- **Backend**: PHP 7/8 + SQLite (`RohData.sql3`)
- **Frontend**: Bootstrap (via `include/bootstrap-head.php` & `bootstrap-footer.php`)
- **Database**: SQLite3 with tables such as `PersonInfoRaw`, `PersonImages`, and lookup tables (`Regiment`, `Rank`, `Country`, etc.)
- **Structure**:
  - `include/` — Shared header, menu, footer
  - `js/` — JavaScript assets
  - `DownLoadImage/` — Locally stored personnel photos
  - `images/` — Static site images

## Project Structure (ROH/)

ROH/

├── index.php

├── person.php

├── mainListPeople.php

├── functions.php

├── *.php (charts & lists)

├── include/

│   ├── menu.php

│   ├── bootstrap-head.php

│   └── footer.php

├── js/

├── DownLoadImage/

├── images/

└── RohData.sql3

## Original Project History
Many years ago (around 1994), a Roll of Honour database was manually created in MS Access from official South African Department of Defence documents and hosted online until ~2017. This project aims to restore and modernise that resource.
