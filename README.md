# GalileoGalileiVA2008Memorial

Questo repository non e' soltanto un progetto web. E' un memoriale digitale.

Dentro ci sono i resti vivi di un'epoca precisa: la VA 2007/2008 del Liceo Scientifico Galileo Galilei. Ci sono il gioco, i commenti, i video, i suoni, i soprannomi, le schermate, i rituali da laboratorio informatico, le circolari, le bidelle, i prof buoni, i prof cattivi, l'ironia da classe chiusa in aula troppe ore e abbastanza sveglia da trasformare tutto in leggenda.

Non e' un archivio freddo. E' una ricostruzione affettiva e tecnica di un piccolo mondo scolastico che, invece di svanire, e' stato rimesso in piedi pezzo per pezzo.

## La storia

Ogni classe si porta dietro un lessico suo: battute, episodi, nomi storpiati, piccole ossessioni, file passati di mano, foto venute male, storie che col tempo diventano piu grandi del fatto da cui erano partite.

La VA del Galileo Galilei del 2007/2008 aveva esattamente questo.

Lezioni, verifiche, recuperi, interrogazioni, laboratori, gite, professori ingestibili, bidelle fuori tempo massimo, circolari comparse nel momento peggiore, forum, CD masterizzati, video montati in casa, giochi nati per scherzo e diventati parte dell'identita di classe. Niente di eroico, semplicemente il modo in cui una classe vera finisce per costruirsi una memoria comune.

Questo progetto nasce per ricordare proprio questo: non una scuola astratta, ma una compagnia di persone che ha passato anni insieme e che, nel frattempo, ha lasciato dietro di sé un piccolo archivio di oggetti, storie e riferimenti condivisi.

In quel mondo nasce anche **Acciacca Prof**, gioco scritto originariamente in Delphi per Windows XP, diventato uno dei simboli di quell'immaginario. Ma oggi il progetto e' piu grande del solo gioco: Acciacca Prof e' uno dei reperti principali di un memoriale piu ampio.

## Cos'e' oggi

`GalileoGalileiVA2008Memorial` e' una versione web che raccoglie e rende navigabili:

- il gioco storico **Acciacca Prof**
- i commenti storici del gioco e del sito
- l'archivio video della classe
- una shell grafica ispirata al desktop Windows XP
- classifiche, sessioni e piccoli strumenti di amministrazione
- materiali e riferimenti usati per ricostruire il comportamento originale

L'obiettivo non e' solo "far funzionare qualcosa", ma ricreare atmosfera, tono, ritmo e memoria.

## Cosa contiene il progetto

- una landing pubblica che introduce il memoriale
- una versione web giocabile di **Acciacca Prof**
- un archivio video con contenuti pubblici e contenuti riservati alla sessione `VA`
- commenti distinti tra storici e nuovi
- leaderboard persistente lato PHP
- un `godpanel` admin per gestire commenti e leaderboard
- asset grafici e sonori recuperati o riallineati al materiale storico

La parte iOS e' stata separata in un repository dedicato:

- [AcciaccaProfIOS](https://github.com/jacmos3/AcciaccaProfIOS)

## Come funziona

Il progetto e' composto da una parte frontend statica e da una piccola parte backend PHP.

### Frontend

La UI principale vive nella cartella `web/`:

- `web/index.html`
  Pagina pubblica del memoriale, usata come presentazione del progetto.

- `web/play.html`
  Cuore dell'esperienza: shell stile Windows XP, gioco, classifica, archivio video, commenti e finestre desktop.

- `web/resources/`
  Asset grafici, audio, wallpaper, icone, cursori e materiali visivi.

- `web/app/`
  Pagine di supporto e privacy.

### Backend

Gli endpoint PHP vivono in `web/api/` e gestiscono:

- login e logout delle sessioni
- sessione `Generica` e sessione `VA`
- leaderboard persistente
- archivio commenti
- streaming media locale
- pannello admin `godpanel`

### Dati locali

I dati runtime stanno principalmente qui:

- `data/comments.json`
- `data/leaderboard.json`

Questi file contengono i dati nuovi salvati localmente. I commenti legacy storici invece vengono anche reiniettati via codice, cosi' restano separati logicamente dalle nuove aggiunte.

### Media

I video non vengono salvati dentro `web/`, ma in:

```text
media/public/
media/va/
```

e sono esposti tramite gli endpoint PHP, cosi' i contenuti riservati non diventano file statici accessibili direttamente.

## Gameplay di Acciacca Prof

All'interno del memoriale, **Acciacca Prof** resta perfettamente centrale.

La logica e' quella storica:

- dai banchi compaiono i professori
- alcuni vanno colpiti
- altri non vanno toccati
- gli errori abbassano punti e voto
- la bidella puo' cambiare il destino della partita
- il Pentathlon aggiunge cinque prove speciali

La versione web cerca di essere fedele nel feeling, ma non e' una semplice copia piatta: e' una ricostruzione ragionata, usando riferimenti Delphi, materiali storici e il porting iOS come appoggio per la logica moderna.

## Stato attuale

La versione web include:

- shell desktop XP
- finestra di gioco trascinabile
- gameplay classico
- Pentathlon completo integrato nella griglia
- login con sessione `Generica` e `VA`
- leaderboard persistente
- archivio video
- commenti storici e nuovi
- `godpanel` per gestione admin

## Struttura del repository

```text
.
├── web/
│   ├── index.html
│   ├── play.html
│   ├── api/
│   ├── resources/
│   └── app/
├── data/
├── media/
└── README.md
```

## Avvio rapido

Serve PHP, perche' sessioni, leaderboard, commenti e streaming media passano dagli endpoint locali.

Avvio consigliato dalla root del repository:

```bash
php -S 127.0.0.1:4183 -t web
```

Poi apri:

```text
http://127.0.0.1:4183/play.html
```

## Configurazione

Per configurare la sessione `VA` e altre password locali:

```text
web/api/config.local.php
```

Parti dal file esempio:

```text
web/api/config.local.php.example
```

## Note di fedelta

Durante la ricostruzione del memoriale e del gioco sono stati usati come riferimenti:

- coordinate e layering Delphi
- asset originali dell'aula e dei personaggi
- copy storico dei commenti
- materiali del sito originale
- porting iOS per alcune logiche moderne

Quindi il progetto non prova a "modernizzare tutto". Prova a ricordare bene.

## Perche' esiste

Perche' certi progetti non servono a lanciare una startup.

Servono a non perdere le tracce.

Servono a ricordare che una classe non e' fatta solo di registri, compiti e voti, ma anche di linguaggi interni, scherzi, file dimenticati, materiali improbabili e memorie che a distanza di anni continuano ad avere senso solo per chi c'era.

Questo repository prova a tenere insieme tutto questo nel modo piu ordinato possibile.

## Link utili

- Landing del memoriale: `web/index.html`
- Gioco / shell: `web/play.html`
- Repo iOS separato: [AcciaccaProfIOS](https://github.com/jacmos3/AcciaccaProfIOS)

## Licenza / note

Questo repository contiene materiale storico, porting moderni, asset, testi e ricostruzioni legati alla memoria della classe. Prima di riutilizzare o redistribuire i contenuti, conviene chiarire bene provenienza degli asset, diritti e contesto.
