# DashboardBackend
Filer &amp; Database til backend setup på dashboard projekt.

Systemet kører PHP/MySql.

### Installation
Filerne downloades og placeres i den ønskede mappe.

Systemet skal køre på en webserver for at virke - enten på en med netadgang eller en lokal løsning - f.eks. Xamp, Wamp eller Lamp osv.

Systemet er desuden afhængig af, at din $_SERVER["DOCUMENT"] er sat ordentligt op. Den skal pege ned i public_html folderen.

### Database 
I mappen data finder du SQL filen som skal importeres til den database, du vil bruge.

Dine database credentials skal skrives ind i filen /core/userapp/dbconf.php.

Tabellerne mh_activity og mh_subject indeholder data om aktiviteter på skolen.

### Adgang
CMS siden kaldes fra følgende URL: www.yoursitename.dk/cms/

Login og adgangskode er admin.
