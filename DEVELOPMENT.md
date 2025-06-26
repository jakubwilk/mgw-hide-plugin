# Development Guide - MGW Hide Content Plugin

## Błędy lintera - wyjaśnienie

Podczas rozwoju pluginu MyBB, narzędzia do analizy kodu (linters) pokazują błędy dla funkcji i stałych MyBB. **To jest normalne i oczekiwane zachowanie.**

### ⚠️ **Fałszywe alarmy lintera**

Wszystkie poniższe błędy to **fałszywe alarmy** - funkcje i stałe będą dostępne w środowisku MyBB:

#### **Funkcje MyBB**
```php
// Funkcje core MyBB - dostępne w środowisku produkcyjnym
rebuild_settings()              // Przebudowuje cache ustawień
change_admin_permission()       // Zarządza uprawnieniami administratora
admin_redirect()               // Przekierowanie w panelu admin
flash_message()                // Wyświetla komunikaty flash
htmlspecialchars_uni()         // Bezpieczne eskejpowanie HTML z Unicode
update_theme_stylesheet_list() // Aktualizuje listę stylów motywu
```

#### **Stałe MyBB**
```php
// Stałe MyBB - dostępne w środowisku produkcyjnym
TIME_NOW                       // Aktualny timestamp Unix
MYBB_ADMIN_DIR                 // Ścieżka do katalogu admin
TABLE_PREFIX                   // Prefiks tabel bazy danych (zastąpione przez $db->table_prefix)

// Stałe walidacji MyBB
MyBB::INPUT_STRING
MyBB::INPUT_INT
MyBB::INPUT_ARRAY
```

#### **Klasy MyBB**
```php
// Klasy ACP MyBB - dostępne w panelu administracyjnym
Form                          // Klasa do generowania formularzy
FormContainer                 // Kontener formularza
Table                         // Klasa do generowania tabel
```

### ✅ **Dlaczego te błędy występują**

1. **Linter działa poza MyBB** - nie ma dostępu do frameworka
2. **Brak definicji** - funkcje MyBB nie są w scope lintera
3. **Środowisko deweloperskie** - plugin będzie działał w MyBB

### 🔧 **Sprawdzenie poprawności kodu**

Aby sprawdzić czy kod jest poprawny:

1. **Zainstaluj plugin w MyBB** - prawdziwy test
2. **Sprawdź logi błędów** - `error_log` serwera
3. **Testuj funkcjonalność** - czy wszystko działa

### 🛠 **Narzędzia developerskie**

#### **Rekomendowane środowisko**
```bash
# Lokalna instalacja MyBB do testów
- XAMPP/WAMP/MAMP
- MyBB 1.8.38
- PHP 8.1+
- MySQL/MariaDB
```

#### **Struktura testowa**
```
mybb_test/
├── inc/plugins/mgw_hide.php
├── admin/modules/config/mgw_hide.php
├── admin/language/english/config_mgw_hide.lang.php
└── [inne pliki MyBB]
```

#### **Testowanie**
1. Zainstaluj plugin w MyBB
2. Aktywuj plugin
3. Testuj wszystkie funkcje
4. Sprawdź logi błędów

### 📝 **Lista funkcji do przetestowania**

#### **Frontend**
- [ ] Parsowanie tagów `[hide][/hide]`
- [ ] Ukrywanie treści dla gości
- [ ] Wyświetlanie treści dla uprawnionych grup
- [ ] Autor zawsze widzi swoją treść
- [ ] Różne szablony dla gości vs zalogowanych

#### **Panel administracyjny**
- [ ] Dodawanie nowych tagów
- [ ] Edycja istniejących tagów
- [ ] Usuwanie tagów (nie domyślny)
- [ ] Edycja szablonów HTML
- [ ] Edycja stylów CSS
- [ ] Ustawienia globalne

#### **Integracja MyBB**
- [ ] Cytowanie postów z ukrytą treścią
- [ ] Wyniki wyszukiwania
- [ ] Edycja postów (tagi pozostają)
- [ ] Cache szablonów
- [ ] Uprawnienia grup

### 🐛 **Debugowanie**

#### **Włączanie debugowania MyBB**
```php
// W config.php
$config['database']['type'] = 'mysqli';
$config['log_pruning'] = array(
    'admin_logs' => 365,
    'mod_logs' => 365,
    'task_logs' => 30,
    'mail_logs' => 180
);
```

#### **Sprawdzanie błędów**
```php
// Dodaj na początku funkcji do debugowania
error_log("MGW Hide Debug: " . print_r($variable, true));
```

#### **Lokalizacja logów**
- Logi PHP: `error_log` serwera
- Logi MyBB: Panel Admin → Tools & Maintenance → System Health
- Logi przeglądarki: Developer Tools → Console

### 📚 **Dokumentacja MyBB**

#### **Przydatne linki**
- [MyBB Plugin Development](https://docs.mybb.com/1.8/development/plugins/)
- [MyBB Hooks](https://docs.mybb.com/1.8/development/plugins/hooks/)
- [MyBB Template System](https://docs.mybb.com/1.8/development/templates/)
- [MyBB Database Methods](https://docs.mybb.com/1.8/development/database-methods/)

#### **Struktura pluginu MyBB**
```php
// Wymagane funkcje pluginu
function pluginname_info()       // Metadane pluginu
function pluginname_install()    // Instalacja (tabele, ustawienia)
function pluginname_is_installed() // Sprawdzenie instalacji
function pluginname_uninstall()  // Deinstalacja
function pluginname_activate()   // Aktywacja (uprawnienia, cache)
function pluginname_deactivate() // Dezaktywacja
```

### ⚙️ **Konfiguracja IDE**

#### **PHPStorm/VSCode**
Można dodać stub dla funkcji MyBB, ale nie jest to konieczne:

```php
// mybb-stubs.php (opcjonalnie)
if (false) {
    function rebuild_settings() {}
    function change_admin_permission($module, $permission, $value) {}
    function admin_redirect($url) {}
    function flash_message($message, $type) {}
    function htmlspecialchars_uni($string) {}
    
    define('TIME_NOW', time());
    define('MYBB_ADMIN_DIR', './admin/');
}
```

### 🎯 **Deployment**

#### **Przed wydaniem**
1. ✅ **Testuj w czystym MyBB**
2. ✅ **Sprawdź wszystkie funkcjonalności**
3. ✅ **Testuj na różnych grupach użytkowników**
4. ✅ **Sprawdź kompatybilność z motywami**
5. ✅ **Przetestuj dezinstalację**

#### **Pakowanie pluginu**
```
mgw_hide_v1.1.0.zip
├── inc/plugins/mgw_hide.php
├── admin/
│   ├── modules/config/mgw_hide.php
│   └── language/english/config_mgw_hide.lang.php
├── README.md
├── INSTALL.md
├── TEMPLATES.md
└── LICENSE
```

### 🔍 **FAQ dla developerów**

**Q: Czy błędy lintera oznaczają problemy z kodem?**  
A: Nie, to fałszywe alarmy. Kod będzie działał w MyBB.

**Q: Jak pozbyć się błędów lintera?**  
A: Można dodać komentarze `@phpstan-ignore-line` lub stworzyć stub, ale nie jest to konieczne.

**Q: Jak testować plugin bez instalacji MyBB?**  
A: Nie da się. Plugin wymaga środowiska MyBB do prawidłowego działania.

**Q: Czy można użyć frameworków zewnętrznych?**  
A: Tak, ale lepiej używać natywnych funkcji MyBB dla kompatybilności.

**Q: Jak debugować problemy z szablonami?**  
A: Sprawdź cache MyBB, składnię zmiennych `{$variable}` i uprawnienia.

---

**Podsumowanie:** Błędy lintera dla funkcji MyBB to normalne zjawisko podczas rozwoju pluginów. Kod jest poprawny i będzie działał w środowisku MyBB. 