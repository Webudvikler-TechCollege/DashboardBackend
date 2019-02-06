# DashboardBackend
Filer &amp; Database til backend setup på dashboard projekt.

Systemet kører PHP/MySql.

### Installation
Filerne downloades og placeres i den ønskede mappe.

Det er vigtigt at forholdet mellem cms mappen og core mappen forbliver det samme.

### Database 
I mappen data finder du SQL filen som skal importeres til den database, du vil bruge.

Dine database credentials skal skrives ind i filen /core/userapp/dbconf.php.

Tabellerne mh_activity og mh_subject indeholder data om aktiviteter på skolen.

### Adgang
CMS siden kaldes fra følgende URL: www.yoursitename.dk/cms/

Login og adgangskode er admin.
