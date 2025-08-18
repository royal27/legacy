# Legacy CMS Chat System (feature/chat-system)

Acest branch conține structura de bază pentru sistemul PHP modular cu permalinks, template gradient (violet, albastru, roșu) și dark mode (negru, violet, albastru), compatibil PHP 8.2.12 + MySQLi.
Include: Toastr, AJAX/jQuery, sistem de instalare, multi-language, dashboard pentru utilizatori, admin panel complet pentru echipă, plugin manager și plugin chat complex.  
Cerințe principale (scurt sumar):

- Structură modulară (plugins, templates, multi-language, roles/permissions)
- Instalare automată cu alegere limbă, configurare DB, creare fondator
- Dashboard/Profil public+privat, roluri/permisiuni editabile
- Sistem de invitații, register/login/logout cu 2FA, Google/Facebook OAuth
- Notificări live, chatroom cu camere publice/private, camere pe roluri, sistem flood, mentenanță, robot de întâmpinare, puncte, bbcode, emoji
- Upload/download fișiere, galerie foto, suport ticket, task management, ban/kick, validare profil, lock account, sistem alerte, plugin licențe
- Admin panel complet pentru gestionare tot sistemul și pluginurile

> Pentru detalii complete, vezi cerințele din conversație!

## Structură inițială

- /install – sistem instalare & configurare
- /templates – template manager, teme gradient/dark
- /plugins – pluginuri (ex: chat, multi-language, etc)
- /dashboard – dashboard utilizatori
- /admin – admin panel & management
- /assets – CSS (gradient & dark), JS, imagini
- /includes – fișiere PHP comune: db, auth, config, router, etc

## Start rapid

1. Accesează site-ul (index.php), dacă nu e instalat, vei fi redirecționat către instalare.
2. Parcurge pașii de instalare: limbă, DB, fondator.
3. După instalare, folosește dashboard/admin panel pentru configurații și activare pluginuri.

## Permalinks

Sistemul accesează paginile prin permalinks configurate din /includes/router.php.
Exemplu: /dashboard, /chatroom, /forum, /profile/{username} etc.

## Template & Design

- Template gradient: Violet, Albastru, Roșu
- Dark mode: Negru, Violet, Albastru (inclusiv borduri, butoane, select, input, textarea)
- Toastr pentru notificări (success/error/info/warning) – JS/CSS
- Responsive, AJAX/jQuery, UX modern

## Pluginuri

- Chat complex, Multi-language, Download, Galerie foto, Forum, Licențe, Alerte, BBCode, Emoji, Lock account, Suport ticket, Tasks etc.
- Instalare/dezinstalare din admin panel
- Fiecare plugin în folder dedicat: /plugins/{plugin_name}

## Multi-language

- Limbile site-ului + admin panel sunt gestionate din admin.
- Orice text e traductibil.
- Adăugare, editare, ștergere limbă din DB.

## Roluri și permisiuni

- Roluri customizabile din admin panel.
- Permisiuni granular pe ramuri/functii/pluginuri.
- Fondatorul are toate drepturile – poate crea, edita, șterge roluri.

## Instalare

1. Navighează la site (index.php)
2. Selectează limba
3. Completează datele DB
4. Creează user fondator

## Plugin Chat – Features

- Camere publice/private
- Camere doar pentru echipă/roluri
- Utilizatori online live
- Stergere mesaj(e)
- Flood/spam setări
- Smilies pe categorii
- BBCode cu butoane
- Video/link auto-preview
- Parolă pe cameră
- Robot întâmpinare
- Puncte pe mesaj
- Mentenanță plugin
- Notificări live
- Administrare camere
- Acces pe permisiuni

## Securitate

- 2FA, OAuth, validare utilizator
- Ban/kick/IP ban
- Lock account cu PIN
- Profil public/privat

## Licențiere

- Sistem independent pentru licențe (MyLicense PHP)
- Activare/dezactivare din admin panel
- Integrare cu plăți (PayPal/Skrill/Card)

## Contribuie

Orice propunere de cod, plugin, template sau funcționalitate se face prin pull request pe acest branch.
