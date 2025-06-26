# Struktura plików - MGW Hide Content Plugin

## 📁 **Obecna struktura (zgodna z MyBB)**

```
mgw_hide/ (v1.1.0)
├── inc/
│   └── plugins/
│       └── mgw_hide.php              # Plugin główny z systemem szablonów
├── admin/
│   ├── modules/
│   │   └── config/
│   │       └── mgw_hide.php          # Panel ACP z edytorami HTML/CSS
│   └── language/
│       └── english/
│           └── config_mgw_hide.lang.php # Pliki językowe
├── mgw_hide.css                      # Style CSS (opcjonalne)
├── README.md                         # Dokumentacja użytkownika
├── INSTALL.md                        # Przewodnik instalacji  
├── TEMPLATES.md                      # Przykłady szablonów i stylów
├── DEVELOPMENT.md                    # Wyjaśnienie błędów lintera
├── STRUCTURE.md                      # Ten plik - dokumentacja struktury
└── LICENSE                           # Licencja MIT
```

## 🎯 **Dlaczego ta struktura?**

### ✅ **Zgodność z MyBB**
- `inc/plugins/` - standardowa lokalizacja pluginów MyBB
- `admin/modules/config/` - moduły panelu administracyjnego
- `admin/language/english/` - pliki językowe dla ACP

### ✅ **Łatwość instalacji**
- Struktura odpowiada 1:1 strukturze MyBB
- Można skopiować katalogi bezpośrednio
- Nie wymaga reorganizacji podczas instalacji

### ✅ **Profesjonalny standard**
- Zgodny z oficjalnymi wytycznymi MyBB
- Kompatybilny z narzędziami do pakowania
- Ułatwia automatyzację instalacji

## 🚀 **Instrukcja instalacji**

### **Metoda 1: Kopiowanie struktury**
```bash
# Skopiuj całe katalogi zachowując strukturę
cp -r mgw_hide/inc/ /path/to/mybb/inc/
cp -r mgw_hide/admin/ /path/to/mybb/admin/
```

### **Metoda 2: Ręczne kopiowanie**
```bash
# Skopiuj pliki do odpowiednich lokalizacji MyBB
mgw_hide/inc/plugins/mgw_hide.php → mybb/inc/plugins/mgw_hide.php
mgw_hide/admin/modules/config/mgw_hide.php → mybb/admin/modules/config/mgw_hide.php
mgw_hide/admin/language/english/config_mgw_hide.lang.php → mybb/admin/language/english/config_mgw_hide.lang.php
```

### **Opcjonalnie: CSS**
```bash
# Jeśli chcesz używać zewnętrznego pliku CSS
mgw_hide/mgw_hide.css → mybb/mgw_hide.css
```

## 📦 **Pakowanie do dystrybucji**

### **ZIP dla użytkowników**
```
mgw_hide_v1.1.0.zip
├── inc/plugins/mgw_hide.php
├── admin/
│   ├── modules/config/mgw_hide.php
│   └── language/english/config_mgw_hide.lang.php
├── mgw_hide.css (opcjonalnie)
├── README.md
├── INSTALL.md
└── LICENSE
```

### **Pełna paczka deweloperska**
```
mgw_hide_dev_v1.1.0.zip
├── inc/plugins/mgw_hide.php
├── admin/
│   ├── modules/config/mgw_hide.php
│   └── language/english/config_mgw_hide.lang.php
├── mgw_hide.css
├── README.md
├── INSTALL.md
├── TEMPLATES.md
├── DEVELOPMENT.md
├── STRUCTURE.md
└── LICENSE
```

## 🔧 **Środowisko deweloperskie**

### **Struktura testowa MyBB**
```
mybb_test/
├── inc/
│   ├── plugins/
│   │   ├── mgw_hide.php                 # Nasz plugin
│   │   └── [inne pluginy]
│   └── [pliki MyBB]
├── admin/
│   ├── modules/config/
│   │   ├── mgw_hide.php                 # Nasz moduł ACP
│   │   └── [inne moduły]
│   ├── language/english/
│   │   ├── config_mgw_hide.lang.php     # Nasze tłumaczenia
│   │   └── [inne pliki językowe]
│   └── [pliki admin MyBB]
└── [pozostałe pliki MyBB]
```

### **Testowanie struktury**
```bash
# Sprawdź czy pliki są w odpowiednich lokalizacjach
ls -la mybb/inc/plugins/mgw_hide.php
ls -la mybb/admin/modules/config/mgw_hide.php
ls -la mybb/admin/language/english/config_mgw_hide.lang.php
```

## 📋 **Checklist wdrożenia**

### **Przed instalacją**
- [ ] Sprawdź kompatybilność MyBB (1.8.x)
- [ ] Zrób backup bazy danych
- [ ] Sprawdź uprawnienia plików
- [ ] Sprawdź czy katalogi istnieją

### **Podczas instalacji**
- [ ] Skopiuj `inc/plugins/mgw_hide.php`
- [ ] Skopiuj `admin/modules/config/mgw_hide.php`
- [ ] Skopiuj `admin/language/english/config_mgw_hide.lang.php`
- [ ] Sprawdź uprawnienia 644 dla plików
- [ ] Sprawdź czy plugin pojawia się na liście

### **Po instalacji**
- [ ] Aktywuj plugin w ACP
- [ ] Skonfiguruj ustawienia podstawowe
- [ ] Dodaj własne tagi (opcjonalnie)
- [ ] Dostosuj szablony i style (opcjonalnie)
- [ ] Przetestuj funkcjonalność

### **Testowanie**
- [ ] Utwórz post z `[hide][/hide]`
- [ ] Sprawdź wyświetlanie dla gości
- [ ] Sprawdź wyświetlanie dla zalogowanych
- [ ] Sprawdź panel administracyjny
- [ ] Przetestuj własne tagi

## 🔄 **Migracja ze starych wersji**

### **Z wersji 1.0.0 do 1.1.0**
1. Dezaktywuj stary plugin
2. Usuń stary `mgw_hide.php` z `/inc/plugins/`
3. Zainstaluj nową wersję zgodnie z instrukcją
4. Aktywuj nowy plugin
5. Skonfiguruj szablony i style w ACP

### **Backup przed aktualizacją**
```sql
-- Backup tabeli tagów
CREATE TABLE mgw_hide_tags_backup AS SELECT * FROM mgw_hide_tags;

-- Backup ustawień
SELECT * FROM mybb_settings WHERE name LIKE 'mgw_hide_%';
```

## 📝 **Notatki dla deweloperów**

### **Struktura jest zgodna z:**
- MyBB Plugin Development Guidelines
- PSR-4 autoloading (w przyszłości)
- Composer packaging standards
- GitHub release standards

### **Zalety tej struktury:**
- Łatwa automatyzacja buildów
- Zgodność z CI/CD
- Prostota dystrybucji
- Standardowa organizacja

---

**Podsumowanie:** Nowa struktura jest w pełni zgodna ze standardami MyBB i ułatwia zarówno instalację jak i rozwój pluginu. 