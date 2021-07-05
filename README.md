# unicards


## Presentazione del progetto
Il progetto consiste nello sviluppo di UniCards, un portale web che ha lo scopo di assistere gli studenti durante la preparazione degli esami. UniCards funziona mediante il metodo delle flashcards: ogni studente realizza dei mazzi di carte in cui sono riportate, suddivise in sezioni, domande e risposte relative all’esame. L’utente può dunque gestire l’inserimento, la modifica e la rimozione di mazzi o di singole carte.


## Login/registrazione
La pagina di login/registrazione consente all’utente di accedere al portale. In ciascun campo avvengono controlli che assicurano l’inserimento di caratteri validi (e non vuoti) e ogni sessione viene registrata sul database accompagnata da informazioni di interesse, come:
- la piattaforma da cui si sta eseguendo l’accesso;
- il browser;
- la versione del browser;
- la data di accesso.

Tali pagine verranno oscurate agli utenti che hanno già eseguito l’accesso e impediranno la visione di aree private a chi deve ancora effettuarlo.

## Editor
Ogni utente, nella propria area privata, può accedere a un editor che consente la modifica dei mazzi già esistenti o la creazione di nuovi. Questo consente di specificare: 

- il titolo;
- il colore dell’anteprima;
- l’università a cui il mazzo fa riferimento (può essere assente); 
- il corso di studio di riferimento.

Oltre a consentire l’aggiunta o la rimozione di carte e sezioni. Ogni mazzo è suddiviso in più sezioni, all’interno delle quali si riuniscono carte facenti riferimento a un medesimo argomento. Un mazzo può essere reso pubblico o privato, nel secondo caso sarà visibile solo al suo creatore e non sarà mostrato nel motore di ricerca.
 
## Schermata  di gioco
Il sito mette a disposizione due differenti modalità:
-Singola: l’utente sfida un mazzo confrontando le proprie risposte con quelle già presenti, segnando per ciascuna di esse se è corretta o meno. 
- Multigiocatore: l’utente crea un gruppo privato in cui può invitare altri giocatori attraverso un link di invito (eventualmente protetto da password) e sfidarli. In questo caso non è prevista la verifica autonoma delle risposte, ma saranno mostrate a schermo domande con risposta a scelta multipla.

In entrambi i casi, a fine partita verranno mostrati i punteggi e le statistiche (che verranno salvati nel database del sito).

## Informazioni Mazzo
Pagina che fornisce le informazioni relative a un mazzo; darà la possibilità di lasciare recensioni e vedere statistiche e gradimenti degli altri utenti.

## Dashboard
La dashboard sarà la schermata principale alla quale l’utente potrà accedere una volta eseguito il login. Questa consentirà di navigare tra le schede del sito e mostrerà i mazzi creati e preferiti, con la possibilità di suddividerli mediante filtri appositi, come: corso, università ecc. La dashboard sarà dotata di navbar (per la navigazione) e sidebar (per mostrare le informazioni dell’utente e accedere alle impostazioni). Cliccando su un mazzo si potrà scegliere se aprire la schermata di gioco, l’editor o eseguire altre operazioni rapide (come aggiungerlo ai preferiti).

## Landing Page
Pagina iniziale del sito, verrà mostrata una breve introduzione sul funzionamento dello stesso e sarà presente una barra di ricerca per trovare i mazzi creati dagli utenti. Tale ricerca avverrà in base a criteri come: nome, università, corso, popolarità ecc…

## Statistiche
Pagina in cui vengono mostrate le statistiche dei migliori utenti ordinati per punteggio.

## Chi siamo
Pagina di informazioni sul progetto in cui sono riportate le informazioni relative ai contatti e alla storia del sito.

## Impostazioni
Pagina che consente all’utente di modificare le proprie impostazioni; permette di scegliere il tema da applicare al sito e di modificare  le informazioni inserite in fase di registrazione come nome utente, password e-mail.
