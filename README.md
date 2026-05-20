# Autorent - Autorendi haldussüsteem

Autorent on PHP ja MySQL baasil ehitatud veebipõhine autorendiplatvorm. Rakendus sisaldab kahte liidest: administratiivset töölauda haldurile ja mugavat rendikeskkonda kliendile.

## 🚀 Funktsioonid

### Kliendi liides
*   **Responsiivne sirvimine**: Sirvi autoparki koos lehekülgede jaotuse ja margipõhise otsinguga.
*   **Lemmikute süsteem**: Lisa meelepärased autod oma lemmikute nimekirja.
*   **Turvaline broneerimine**: Rendi autosid valitud perioodiks koos automaatse koguhinna arvutamisega.
*   **Kattuvuse kaitse**: Intelligentne loogika, mis takistab sama auto või kliendi topeltbroneeringuid samal perioodil.
*   **Rendiajalugu**: Jälgi oma praeguseid, tulevasi ja möödunud rentimisi koos oleku märguannetega.
*   **Profiili haldus**: Võimalus uuenda panganduse andmeid ja muuta konto parooli.

### Admini töölaud
*   **Autopargi haldus**: Täielik kontroll autode andmebaasi üle (lisa, vaata, muuda, kustuta).
*   **Operatiivne ülevaade**: Jaotatud vaade, mis näitab hetke autoparki, aktiivseid rente ja tulevasi broneeringuid.
*   **Broneeringute tühistamine**: Administraatoril on õigus tühistada tulevasi broneeringuid.
*   **Admini profiil**: Turvaline paroolihaldus halduri kontole.

## 🛠️ Tehnoloogiad

*   **Backend**: PHP 8.x
*   **Andmebaas**: MariaDB / MySQL
*   **Frontend**: Bootstrap 5, Bootstrap Icons
*   **Turvalisus**: Bcrypt paroolide räsimine, Prepared Statements kaitseks SQL-süstide vastu ja rollipõhine sessioonihaldus.

## ⚙️ Paigaldamine ja seadistamine

1.  **Klooni hoidla**:
    ```bash
    git clone https://github.com/iveiks/autorent.git
    ```

2.  **Andmebaasi seadistamine**:
    *   Dockeri kasutamisel luuakse andmebaas ja tabelid automaatselt failist `db/cars_rent.sql`.

3.  **Docker (Valikuline)**:
    Kui kasutad Dockerit, siis käivita:
    ```bash
    docker-compose up -d
    ```

4.  **Kasutamine**:
    Rakendus on kättesaadav aadressil: [http://localhost:8080](http://localhost:8080). Esimene käivitus võib võtta kuni 30 sekundit, kuni andmebaas initsialiseeritakse.

## 🔑 Sisselogimisandmed

Administraatori paneelile pääsemiseks kasuta järgmisi andmeid:
*   **Kasutajanimi**: `boss`
*   **Parool**: `Passw0rd`

Kliendid saavad luua oma konto avalehel asuva nupu **Registreeri** kaudu.

## 📝 Litsents
See projekt on loodud õppe-eesmärgil osana PHP aluste kursusest.

---
*Arendatud Veiko poolt*
