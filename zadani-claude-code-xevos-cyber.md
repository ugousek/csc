# Zadání pro Claude Code – XEVOS Cyber Security Center

## Přehled projektu

WordPress web s custom tématem pro XEVOS Cyber Security Center. Veškerý obsah editovatelný přes ACF Pro. Design vychází z Figma podkladů: https://www.figma.com/design/YDDOwsAjDpG1HvysQvaNlT/WEB?node-id=688-3800&t=q9gz5EOnM8It3R7z-0

**Technologický stack:** WordPress 6.x, PHP 8.1+, custom téma (bez page builderů), ACF Pro, vanilla JS / jQuery, SCSS → CSS, Contact Form 7 nebo Gravity Forms.

**Prostředí:** lokální vývoj XAMPP (`E:\htdocs\`), deployment na sdílený hosting (Forpsi/Wedos).

---

## Modul 1 – Setup & deployment

- Čistá instalace WordPress
- Custom téma (starter: prázdné téma, ne Underscores/Sage – jen holá struktura `functions.php`, `style.css`, `index.php`, šablony)
- `.gitignore`, `wp-config.php` pro local/staging/production
- Composer autoload pro PHP třídy tématu
- Build pipeline: SCSS → CSS (PostCSS autoprefixer), JS bundling (Vite nebo Webpack)
- ACF Pro aktivace (licence klíč přes `wp-config.php` konstantu)

---

## Modul 2 – Design systém & globální prvky

### Typografie & barvy
- Implementovat barvy a fonty přesně dle Figma designu
- CSS custom properties (`--color-primary`, `--color-secondary`, `--font-heading`, `--font-body` atd.)
- Typografická škála: h1–h6, body, small, caption – vše z Figma

### Hlavička (header)
- Logo + navigace (desktop: horizontální menu, podpoložky jako dropdown)
- Sticky header při scrollu (zmenšená verze)
- CTA tlačítko v hlavičce
- ACF: logo (image field), CTA text + URL, telefonní číslo

### Hamburger menu (mobil)
- Full-screen overlay nebo slide-in panel
- Animovaný hamburger icon (3 čáry → X)
- Podpoložky: accordion rozbalení
- Kontaktní info ve spodní části menu

### Patička (footer)
- Sloupce: logo + popis, navigační odkazy, kontakt, sociální sítě
- ACF: vše editovatelné (repeater pro soc. sítě, WYSIWYG pro popis)
- Copyright řádek s dynamickým rokem

### Globální komponenty (partials)
- `components/button.php` – primární, sekundární, outline, ghost varianty
- `components/card.php` – služba, aktualita, školení, recenze
- `components/section-heading.php` – nadpis + podnadpis + dekorace
- `components/cta-banner.php` – opakující se CTA sekce
- Každý komponent přijímá parametry přes `$args` pole

---

## Modul 3 – Homepage

Sekce (každá s ACF přepínačem `zobrazit_sekci` true/false):

1. **Hero** – velký nadpis, podnadpis, CTA tlačítko, background image/video. ACF: heading, subheading, CTA text + URL, background media.

2. **Přehled služeb** – slider nebo grid karet. ACF: repeater (ikona, název, popis, URL). Swiper.js pro slider.

3. **Kybernetické testování** – sekce s popisem a CTA. ACF: heading, text (WYSIWYG), obrázek, CTA.

4. **Kyber politika** – popis služby. ACF: heading, text, obrázek, seznam bodů (repeater).

5. **Eventy / Školení** – výpis 3 nejbližších školení (dynamicky z CPT). ACF: heading, počet zobrazených.

6. **Aktuality** – 3 nejnovější články (dynamicky z CPT). ACF: heading, počet zobrazených.

7. **Recenze** – slider referencí. ACF: heading + dynamický výpis z CPT/repeater.

8. **CTA sekce** – závěrečná výzva k akci. ACF: heading, text, CTA button.

---

## Modul 4 – ACF editovatelné sekce

### Architektura ACF field groups

```
Field Group: "Nastavení webu" (options page)
├── Logo
├── Kontaktní údaje (tel, email, adresa)
├── Sociální sítě (repeater)
├── Firemní údaje (IČO, DIČ, název firmy)
└── Defaultní OG image

Field Group: "Homepage"
├── Hero sekce
│   ├── zobrazit_sekci (true/false)
│   ├── heading (text)
│   ├── subheading (textarea)
│   ├── cta_text + cta_url
│   └── background (image)
├── Služby sekce
│   ├── zobrazit_sekci
│   ├── heading
│   └── sluzby (repeater: ikona, název, popis, url)
├── [další sekce analogicky]
...

Field Group: "Školení – detail" (CPT: skoleni)
├── popis (WYSIWYG)
├── cena (number)
├── cena_s_dph (number, computed nebo manuální)
├── kategorie_skoleni (taxonomy)
├── typ (select: online/prezenční/hybrid)
├── lektori (relationship → CPT nebo repeater)
├── harmonogram (repeater: čas, aktivita)
├── osnova (repeater: bod)
├── terminy (repeater)
│   ├── datum (date picker)
│   ├── cas_od, cas_do (time picker)
│   ├── misto (text)
│   ├── kapacita (number)
│   └── pocet_registraci (number, read-only, computed)
└── objednavkovy_formular (true/false)
```

### Pravidla
- Každá sekce na homepage má `zobrazit_sekci` toggle
- Obrázky: vždy s `return format: array` (url + alt + sizes)
- WYSIWYG pole: toolbar omezit na potřebné formátovací prvky
- Validace: required pole označit, number pole s min/max
- ACF JSON sync: `acf-json/` složka v tématu

---

## Modul 5 – Aktuality (blog)

### Custom Post Type: `aktualita`
- Labels CZ: Aktualita / Aktuality
- Supports: title, editor, thumbnail, excerpt
- Archive: ano (`/aktuality/`)
- Has archive, publicly queryable, show in REST

### Taxonomie: `kategorie-aktualit`
- Hierarchická (jako categories)
- Předvyplněné termy: Azure, Cloud, NIS2, Kybernetická bezpečnost, GDPR, Školení

### Archiv stránka (`archive-aktualita.php`)
- Grid výpis (3 sloupce desktop, 2 tablet, 1 mobil)
- Filtrace dle kategorií (AJAX, bez reloadu stránky)
- Řazení: nejnovější (default), nejstarší
- Stránkování: AJAX load more nebo numbered pagination
- Karta aktuality: thumbnail, kategorie badge, datum, nadpis, excerpt, odkaz

### Detail (`single-aktualita.php`)
- Hero s featured image
- Breadcrumbs
- Obsah článku (the_content)
- Autor + datum publikace
- Sidebar: související aktuality (stejná kategorie, max 3)
- Share tlačítka (FB, LinkedIn, X, kopírovat URL)
- Navigace předchozí/další článek

---

## Modul 6 – Školení

### Custom Post Type: `skoleni`
- Labels CZ: Školení / Školení
- Archive: `/skoleni/`
- Supports: title, thumbnail

### Taxonomie: `kategorie-skoleni`
- Hierarchická
- Termy: Kybernetická bezpečnost, NIS2, Cloud Security, GDPR, Awareness

### Archiv (`archive-skoleni.php`)
- Grid/list výpis
- Filtry: kategorie, typ (online/prezenční), dostupné termíny (budoucí datum)
- AJAX filtrace
- Karta: thumbnail, název, nejbližší termín, cena, typ badge, CTA tlačítko

### Detail (`single-skoleni.php`)
- Hero sekce s názvem a krátkým popisem
- Tabs nebo sekce:
  - **O školení** – WYSIWYG popis
  - **Osnova** – číslovaný seznam z ACF repeater
  - **Harmonogram** – tabulka čas/aktivita
  - **Lektoři** – karty (foto, jméno, bio)
  - **Termíny** – tabulka termínů s dostupností a CTA "Objednat"
- Objednávkový formulář (viz Modul 7)

### Správa kapacity
```php
// Logika v functions.php nebo custom třídě
function get_termin_dostupnost($skoleni_id, $termin_index) {
    $terminy = get_field('terminy', $skoleni_id);
    $termin = $terminy[$termin_index];
    $kapacita = (int) $termin['kapacita'];
    $registrace = (int) $termin['pocet_registraci'];
    
    return [
        'kapacita' => $kapacita,
        'registrace' => $registrace,
        'volna_mista' => $kapacita - $registrace,
        'plne' => $registrace >= $kapacita
    ];
}
```
- Po úspěšné objednávce: inkrementovat `pocet_registraci` v ACF repeater
- Pokud `plne === true`: skrýt tlačítko "Objednat", zobrazit "Kapacita naplněna"
- Admin notifikace při naplnění 80 % a 100 % kapacity

---

## Modul 7 – Formuláře

### Kontaktní formulář
- Pole: jméno, email, telefon (nepovinné), předmět (select), zpráva
- Validace: client-side (HTML5 + JS) + server-side (CF7/GF)
- Antispam: honeypot + reCAPTCHA v3 (nebo Turnstile)
- E-mail notifikace: adminovi + autoresponder odesílateli
- Success/error stavy s animací

### Objednávkový formulář školení
- Pole: jméno, příjmení, email, telefon, firma, IČO, DIČ (nepovinné), fakturační adresa, vybraný termín (select – dynamicky z ACF termínů), poznámka
- Hidden pole: ID školení, název školení, cena
- Validace IČO: formát 8 číslic
- Po odeslání: vytvoří CPT "Objednávka" (Modul 17), přesměruje na Comgate platbu (Modul 14)
- Antispam: honeypot + reCAPTCHA

---

## Modul 8 – Recenze

### Varianta A (doporučená): CPT `recenze`
- Pole (ACF): jméno, pozice/firma, text recenze, foto (nepovinné), hodnocení (1–5 hvězd, nepovinné)
- Výpis: Swiper.js slider na homepage a dalších stránkách
- Admin: snadná správa, řazení drag & drop (plugin nebo custom meta)

### Varianta B: ACF Repeater na Options page
- Pokud klient preferuje jednodušší správu

### Frontend
- Slider s autoplay, pauza při hoveru
- Karty: avatar/iniciály, jméno, firma, citace
- Responzivní: 3 karty desktop, 2 tablet, 1 mobil

---

## Modul 9 – Stránka Kybernetické testování

Template: `page-kyberneticke-testovani.php`

Sekce dle designu (každá s ACF toggle):

1. **Hero** – heading, subheading, CTA, background
2. **Webové aplikace** – popis testování web aplikací, seznam bodů
3. **Slider** – vizuální slider (Swiper.js) s case studies nebo typy testů
4. **Penetrační testy** – popis, výhody, ikony
5. **Postup testování** – timeline/stepper (kroky procesu)
6. **Co získáte** – benefit sekce s ikonami a popisy
7. **Aktuality** – dynamický výpis relevantních aktualit (filtr dle kategorie)
8. **Recenze** – reuse komponenty z Modulu 8
9. **Poptávkový formulář** – inline formulář (CF7) s poli: jméno, email, firma, typ testu (select), popis požadavku

---

## Modul 10 – Obchodní podmínky / GDPR stránky

- Template: `page-legal.php` nebo default s ACF
- WYSIWYG obsah (celostránkový editor)
- ACF: datum poslední aktualizace
- TOC (table of contents) generovaný z h2/h3 nadpisů v obsahu (JS)
- Stránky: Obchodní podmínky, Zásady ochrany osobních údajů, Zásady cookies

---

## Modul 11 – Cookie lišta

- Plugin: Real Cookie Banner nebo Complianz (preferovaný dle klienta)
- Kategorie: nezbytné, analytické, marketingové
- Integrace s GTM: fire tags až po consent
- Stylování lišty dle designu webu (custom CSS override)
- CZ lokalizace textů
- Link na Zásady cookies v patičce

---

## Modul 12 – Vyhledávání

### AJAX live search (našeptávač)
```javascript
// Trigger: input event s debounce 300ms, min 3 znaky
// Endpoint: custom REST API route nebo admin-ajax.php
// Prohledává: aktuality, školení, stránky
// Vrací: max 5 výsledků seskupených dle typu
```

### Našeptávač UI
- Dropdown pod search inputem
- Skupiny: Školení, Aktuality, Stránky
- Každý výsledek: nadpis + krátký excerpt + typ badge
- Keyboard navigace (šipky + Enter)
- ESC zavře, klik mimo zavře

### Stránka výsledků (`search.php`)
- Custom template
- Grid výpis výsledků
- Zvýraznění hledaného výrazu v excerpts
- Počet výsledků
- Stránkování
- Prázdný stav: "Nic nenalezeno" + návrhy

---

## Modul 13 – Responzivita

### Breakpointy
```scss
$breakpoints: (
  'mobile':  480px,
  'tablet':  768px,
  'desktop': 1024px,
  'wide':    1280px,
  'ultra':   1440px
);
```

### Zásady
- Mobile-first přístup (min-width media queries)
- Testovat dle dodaných mobilních screenshotů z Figma
- Obrázky: `srcset` + `sizes` atributy, WebP s JPEG fallback
- Touch targets: min 44×44px
- Hamburger menu pod 1024px
- Tabulky: horizontální scroll na mobilu
- Formuláře: plná šířka inputů na mobilu

---

## Modul 14 – Platební brána Comgate

### Custom WP plugin: `xevos-comgate-payment`

#### Struktura
```
xevos-comgate-payment/
├── xevos-comgate-payment.php       (hlavní plugin soubor)
├── includes/
│   ├── class-comgate-api.php       (API komunikace)
│   ├── class-payment-handler.php   (zpracování plateb)
│   ├── class-order-manager.php     (správa objednávek)
│   └── class-invoice-generator.php (viz Modul 15)
├── templates/
│   ├── payment-success.php
│   ├── payment-failure.php
│   └── payment-pending.php
├── admin/
│   └── class-admin-settings.php    (nastavení v WP admin)
└── assets/
```

#### Comgate API flow
1. Uživatel odešle objednávkový formulář
2. Plugin vytvoří CPT "Objednávka" se stavem `pending`
3. Plugin volá Comgate API `createPayment`:
   - `merchant`: z nastavení
   - `price`: cena v haléřích (×100)
   - `curr`: CZK
   - `label`: název školení
   - `refId`: ID objednávky
   - `method`: ALL (výběr metody na straně Comgate)
   - `prepareOnly`: true
4. Přesměrování uživatele na Comgate platební bránu
5. Comgate callback (server-to-server):
   - Endpoint: `/wp-json/xevos/v1/comgate-callback`
   - Ověření HMAC podpisu
   - Aktualizace stavu objednávky: `paid` / `cancelled` / `authorized`
6. Návratové stránky: success / failure / pending

#### Refundace
- Admin akce v detailu objednávky: tlačítko "Refundovat"
- Volání Comgate API `refundPayment`
- Aktualizace stavu na `refunded`
- E-mail zákazníkovi o refundaci

#### Nastavení (WP admin → Nastavení → Comgate)
- Merchant ID
- Secret key
- Test/Production mode toggle
- Povolené platební metody

---

## Modul 15 – PDF faktura

### Generování
- Knihovna: TCPDF nebo Mpdf (PHP)
- Trigger: po úspěšném Comgate callbacku (stav `paid`)
- Uložení: `wp-content/uploads/faktury/2026/FV-2026-0001.pdf`

### Obsah faktury
- Hlavička: logo firmy, název, adresa, IČO, DIČ
- Číslo faktury: `FV-{ROK}-{SEKVENCE}` (auto-increment)
- Variabilní symbol: ID objednávky
- Datum vystavení, datum zdanitelného plnění, datum splatnosti
- Odběratel: jméno/firma, IČO, DIČ, adresa (z objednávky)
- Položky: název školení, termín, počet ks, cena bez DPH, sazba DPH, cena s DPH
- Rekapitulace DPH (základ, DPH 21 %, celkem)
- Způsob úhrady: platba kartou (uhrazeno)
- Patička: bankovní spojení, QR kód pro platbu (nepovinné)

### Doručení
- Příloha potvrzovacího e-mailu zákazníkovi
- Přístupná v admin detailu objednávky (download link)

---

## Modul 16 – E-mailové šablony

### Šablony (HTML, responzivní, Outlook-kompatibilní)

1. **Potvrzení objednávky** – po odeslání formuláře (stav pending)
   - Shrnutí objednávky, školení, termín, cena
   - Link na platbu (pokud nebyla dokončena)

2. **Potvrzení platby** – po úspěšném Comgate callbacku
   - Děkujeme za platbu
   - Detail objednávky
   - PDF faktura jako příloha
   - Informace o školení (datum, místo, co si vzít)

3. **Notifikace adminovi** – při nové objednávce
   - Odkaz do WP admin na detail objednávky

4. **Registrace na školení – potvrzení** – po zaplacení
   - Detail školení, termín, místo
   - Harmonogram dne
   - Kontakt na organizátora

5. **Zrušení účasti** – admin akce
   - Info o refundaci
   - Kontakt pro dotazy

6. **Připomenutí před školením** – WP CRON, 3 dny a 1 den před
   - Datum, čas, místo
   - Co si vzít / jak se připojit (online)

### Technické požadavky
- Table-based layout (Outlook kompatibilita)
- Inline CSS (nepodporovat `<style>` v `<head>` pro Outlook)
- Max šířka: 600px
- Fallback fonty: Arial, Helvetica, sans-serif
- Dark mode: `@media (prefers-color-scheme: dark)` + `mso-` conditional comments
- Testovat: Outlook 2019/365, Gmail, Seznam.cz, Apple Mail
- Odesílání: `wp_mail()` s HTML content type a custom headers

---

## Modul 17 – Dashboard objednávek

### Custom Post Type: `objednavka`
- Capabilities: pouze admin (ne veřejné)
- Supports: title (auto-generovaný: `OBJ-{ROK}-{ID}`)

### ACF pole
- jmeno, prijmeni, email, telefon
- firma, ico, dic
- fakturacni_adresa (group: ulice, mesto, psc)
- skoleni (post object → CPT školení)
- termin (text – formátované datum+čas)
- castka (number)
- stav_platby (select: pending / paid / cancelled / refunded)
- comgate_transaction_id (text)
- datum_objednavky (date, auto)
- pdf_faktura (file – link na vygenerovanou fakturu)

### Admin přehled (list table)
Custom sloupce v `edit.php`:
- Číslo objednávky
- Jméno + příjmení
- Email
- Firma
- Školení (link na detail)
- Termín
- Částka
- Stav platby (barevný badge: žlutá/zelená/červená/šedá)
- Datum

### Filtry
- Dropdown filtr: stav platby
- Dropdown filtr: školení
- Řazení dle data (default: nejnovější)

### Detail objednávky
- Kompletní přehled všech polí
- Akce:
  - Změnit stav platby (manuálně)
  - Refundovat (volá Comgate API)
  - Stáhnout/přegenerovat PDF fakturu
  - Znovu odeslat potvrzovací email
- Historie změn stavů (meta log)

---

## Modul 18 – 404, Favicon, OG meta tags

### 404 stránka (`404.php`)
- Design dle Figma
- Heading, subheading, ilustrace/animace
- Vyhledávací pole
- CTA tlačítko "Zpět na hlavní stránku"
- Návrhy stránek (nejnavštěvovanější nebo manuální výběr přes ACF)

### Favicon
- Generovat všechny velikosti z jednoho SVG/PNG:
  - `favicon.ico` (16×16, 32×32)
  - `apple-touch-icon.png` (180×180)
  - `favicon-32x32.png`, `favicon-16x16.png`
  - `android-chrome-192x192.png`, `android-chrome-512x512.png`
  - `site.webmanifest`
- ACF na Options page: upload favicon zdrojového souboru

### OG meta tags
- Yoast SEO generuje automaticky
- Defaultní OG image z ACF Options (pokud stránka nemá vlastní)
- Twitter Card: `summary_large_image`
- Validovat: Facebook Sharing Debugger, Twitter Card Validator

---

## Modul 19 – SEO základ

- Yoast SEO Pro (nebo free)
- Meta descriptions: homepage, kybernetické testování, školení archiv, kontakt
- `robots.txt`: allow all, sitemap reference, disallow `/wp-admin/`
- XML sitemap: automaticky přes Yoast (aktuality + školení + stránky)
- Schema markup:
  - Organization (homepage)
  - Course (školení)
  - Article (aktuality)
  - BreadcrumbList
- Heading hierarchy audit: jeden h1 na stránku
- Lazy loading obrázků: native `loading="lazy"`
- Core Web Vitals optimalizace: minimalizovat CSS/JS, critical CSS inline

---

## Modul 20 – Testování & předání

### Cross-browser testování
- Chrome, Firefox, Safari, Edge (poslední 2 verze)
- iOS Safari, Android Chrome
- Outlook e-maily (2019, 365, webmail)

### Checklist před předáním
- [ ] Všechny stránky responzivní (mobile, tablet, desktop)
- [ ] Formuláře funkční s validací a notifikacemi
- [ ] Comgate platby funkční (test mode + production)
- [ ] PDF faktury se generují správně
- [ ] E-mailové šablony vypadají správně ve všech klientech
- [ ] Cookie lišta funkční, GTM fires po consent
- [ ] Vyhledávání funguje (live search + stránka výsledků)
- [ ] 404 stránka funguje
- [ ] SEO: meta, sitemap, robots.txt, schema
- [ ] ACF pole editovatelná a uložitelná bez chyb
- [ ] Kapacita školení se správně počítá a uzavírá
- [ ] Performance: PageSpeed Insights > 85 (mobile + desktop)
- [ ] Bezpečnost: aktualizace, silná hesla, Wordfence/Sucuri basic

### Dokumentace pro klienta
- Jak přidávat/editovat aktuality
- Jak spravovat školení a termíny
- Jak sledovat objednávky
- Jak editovat homepage sekce přes ACF
- Jak přidávat recenze
- Základní troubleshooting (cache, pluginy)

### Přístupy k předání
- WP admin (admin + editor účet)
- FTP/SFTP
- Databáze (pokud potřeba)
- Git repozitář
- Comgate merchant panel
- Google Analytics + GTM

---

## Struktura tématu

```
xevos-cyber-theme/
├── style.css
├── functions.php
├── front-page.php
├── header.php
├── footer.php
├── sidebar.php
├── search.php
├── searchform.php
├── 404.php
├── page.php
├── page-kyberneticke-testovani.php
├── page-legal.php
├── single.php
├── single-aktualita.php
├── single-skoleni.php
├── archive-aktualita.php
├── archive-skoleni.php
├── inc/
│   ├── setup.php              (theme supports, menus, image sizes)
│   ├── enqueue.php            (CSS/JS)
│   ├── cpt.php                (custom post types)
│   ├── taxonomies.php
│   ├── acf.php                (ACF helpers, options pages)
│   ├── ajax-handlers.php      (live search, filtrace)
│   ├── email.php              (custom wp_mail wrappers)
│   ├── helpers.php            (utility functions)
│   └── admin/
│       ├── order-columns.php  (custom admin columns)
│       └── order-filters.php
├── template-parts/
│   ├── components/
│   │   ├── button.php
│   │   ├── card-aktualita.php
│   │   ├── card-skoleni.php
│   │   ├── card-recenze.php
│   │   ├── section-heading.php
│   │   └── cta-banner.php
│   ├── homepage/
│   │   ├── hero.php
│   │   ├── sluzby.php
│   │   ├── kyber-testovani.php
│   │   ├── kyber-politika.php
│   │   ├── eventy.php
│   │   ├── aktuality.php
│   │   ├── recenze.php
│   │   └── cta.php
│   └── kyber-testovani/
│       ├── hero.php
│       ├── web-aplikace.php
│       ├── slider.php
│       ├── penetracni-testy.php
│       ├── postup.php
│       ├── co-ziskate.php
│       └── formular.php
├── acf-json/                   (ACF field group JSON sync)
├── assets/
│   ├── scss/
│   │   ├── _variables.scss
│   │   ├── _mixins.scss
│   │   ├── _typography.scss
│   │   ├── _buttons.scss
│   │   ├── _forms.scss
│   │   ├── _header.scss
│   │   ├── _footer.scss
│   │   ├── _homepage.scss
│   │   ├── _skoleni.scss
│   │   ├── _aktuality.scss
│   │   └── style.scss
│   ├── js/
│   │   ├── main.js
│   │   ├── navigation.js
│   │   ├── live-search.js
│   │   ├── ajax-filter.js
│   │   └── swiper-init.js
│   ├── img/
│   └── fonts/
├── email-templates/
│   ├── order-confirmation.php
│   ├── payment-confirmation.php
│   ├── admin-notification.php
│   ├── registration-confirmation.php
│   ├── cancellation.php
│   └── reminder.php
└── languages/
    └── cs_CZ.po / .mo
```

---

## Závislosti (npm / composer)

### npm (devDependencies)
- `sass` – SCSS kompilace
- `autoprefixer` + `postcss` – vendor prefixy
- `vite` – bundling a dev server (nebo `webpack`)

### Frontend knihovny
- `swiper` – slidery (hero, recenze, služby)
- Žádný jQuery pokud není nutný (CF7 ho vyžaduje)

### Composer
- Žádné povinné PHP závislosti mimo WordPress
- Plugin Comgate: `mpdf/mpdf` nebo `tecnickcom/tcpdf` pro PDF generování

---

## Důležité konvence

- **Kódování:** UTF-8 všude
- **Jazyk kódu:** angličtina (funkce, třídy, komentáře)
- **Jazyk obsahu:** čeština
- **Prefix:** `xevos_` pro funkce, `xevos-` pro CSS třídy (BEM: `xevos-card__title--active`)
- **PHP:** WordPress Coding Standards, namespace `Xevos\CyberTheme`
- **Escapování:** vždy `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()` pro výstup
- **Nonce:** na všechny AJAX requesty a formuláře
- **Textdoména:** `xevos-cyber` pro překlady (`__()`, `_e()`)
