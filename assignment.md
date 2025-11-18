# Tietokantajärjestelmät (SQL), Tampereen yliopisto

## Ohjelmointitehtävä (muunnelma viikkoharjoituksista)

a) Tee tietokantaan tilit-relaatio: TILIT(tilinumero, omistaja, summa). Lisää tietokantaan
muutama tilin omistaja ja tilitiedot.

b) Tee tilinsiirtoa varten PHP-ohjelma, joka kysyy lomakkeen kautta

1. Siirrettävän summan,
2. Veloitettavan tilinumeron
3. Tilinumeron, jonne summa siirretään.

sekä tekee tilinsiirron tietokantaan. Tilinsiirto pitää määritellä tietokantatapahtumana. Jos
tilinsiirto epäonnistui, niin ohjelma antaa virheilmoituksen. Jos tilinsiirto onnistui, niin
ohjelma siirtyy toiselle sivulle ja antaa ilmoituksen: ”x on siirtänyt z euroa henkilölle y.” (x ja
y ovat tilien omistajia ja z on siirrettävä summa). Käytä tietojen siirtoon sessiomuuttujia.

Palautus:

1. Palauta lähdekoodit.
2. Palauta linkki, jossa ohjelmaa voi testata.

Liite. Tapahtumat PostgreSQL:ssä

- PostgreSQL:ssä on käytössä tapahtuman kontrollirakenteet: BEGIN (aloitus),
    COMMIT (sitoutuminen) ja ROLLBACK (peruutus)
- Tapahtuman kontrollikomennot lähetään tietokannalle ’hajautetusti’ PHP-ohjelman
    eri haaroissa.
- Yhdessä if-haarassa voidaan peruuttaa tapahtuma ja toisessa sitoutua siihen
- Seuraavaksi PHP-esimerkki:

pg_query('BEGIN')
or die('Ei onnistuttu aloittamaan tapahtumaa:'. pg_last_error());

$tulos = pg_query('UPDATE '. $taulu
.' SET saldo = saldo - '. $summa.
' WHERE tilinro = \''. $tililta. '\'' .' AND saldo > '. $summa)
or die('Virhe ensimmäisessä päivityksessä: '. pg_last_error());

if (pg_affected_rows($tulos) != 1)
{
pg_query('ROLLBACK')
or die('Ei onnistuttu perumaan tapahtumaa: '. pg_last_error());
return 'Lähdetilin tilinumero on väärä tai saldoa ei ole tarpeeksi.';
}
...etc

pg_query('COMMIT')
or die('Ei onnistuttu hyväksymään tapahtumaa: '. pg_last_error());
...etc.


