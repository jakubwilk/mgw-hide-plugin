# Niestandardowe wiadomości HTML - MGW Hide Content

## Nowa funkcjonalność: Custom Messages per Tag

Od wersji 1.0.17+ plugin MGW Hide Content obsługuje definiowanie niestandardowych wiadomości HTML dla każdego tagu osobno.

## Jak to działa?

### Domyślne zachowanie (bez custom message):
```
Tag bez custom message → używa globalnej wiadomości z ustawień pluginu
```

### Nowe zachowanie (z custom message):
```
Tag z custom message → używa niestandardowej wiadomości HTML
```

## Aktualizacja istniejących instalacji

Jeśli masz już zainstalowany plugin, uruchom skrypt aktualizacji:

```
http://yoursite.com/admin/mgw_hide_update_schema.php
```

**⚠️ Usuń ten plik po uruchomieniu!**

## Przykłady użycia

### 1. Tag VIP z niestandardowym stylem

**Tag:** `[vip]`  
**Custom Message:**
```html
<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; text-align: center; margin: 15px 0;">
    <div style="font-size: 24px; margin-bottom: 10px;">👑</div>
    <h3 style="margin: 0; color: white;">Treść VIP</h3>
    <p style="margin: 10px 0 0 0;">Zostań członkiem VIP aby zobaczyć ekskluzywną treść!</p>
    <a href="/vip-signup" style="background: rgba(255,255,255,0.2); color: white; padding: 8px 16px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;">Wykup VIP</a>
</div>
```

### 2. Tag Premium z Bootstrap

**Tag:** `[premium]`  
**Custom Message:**
```html
<div class="alert alert-warning d-flex align-items-center" role="alert">
    <i class="fas fa-crown me-3" style="font-size: 24px; color: #f39c12;"></i>
    <div>
        <h5 class="alert-heading mb-1">Treść Premium 💎</h5>
        <p class="mb-2">Ta treść jest dostępna tylko dla użytkowników Premium.</p>
        <a href="/premium" class="btn btn-outline-warning btn-sm">Zostań Premium</a>
    </div>
</div>
```

### 3. Tag dla moderatorów

**Tag:** `[mod]`  
**Custom Message:**
```html
<div style="background: #e74c3c; color: white; padding: 15px; border-radius: 5px; border-left: 5px solid #c0392b;">
    <strong>🛡️ Treść dla moderatorów</strong><br>
    Ta treść jest dostępna tylko dla zespołu moderacyjnego.
</div>
```

### 4. Tag z animacją CSS

**Tag:** `[exclusive]`  
**Custom Message:**
```html
<div class="exclusive-content" style="background: #2c3e50; color: #ecf0f1; padding: 20px; border-radius: 8px; position: relative; overflow: hidden;">
    <div style="position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent); animation: shimmer 2s infinite;"></div>
    <div style="position: relative; z-index: 1;">
        <span style="font-size: 20px;">🔒</span>
        <strong>Ekskluzywna treść</strong><br>
        Dostęp tylko dla wybranych użytkowników.
    </div>
</div>

<style>
@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}
</style>
```

### 5. Tag z formularzem logowania

**Tag:** `[members]`  
**Custom Message:**
```html
<div style="background: #f8f9fa; border: 2px dashed #dee2e6; padding: 20px; text-align: center; border-radius: 10px;">
    <h4 style="color: #495057; margin-bottom: 15px;">🔐 Treść dla członków</h4>
    <p style="color: #6c757d; margin-bottom: 15px;">Zaloguj się aby zobaczyć tę treść</p>
    <div style="display: inline-flex; gap: 10px;">
        <a href="/member.php?action=login" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Zaloguj się</a>
        <a href="/member.php?action=register" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Zarejestruj się</a>
    </div>
</div>
```

## Zarządzanie Custom Messages

### W panelu administracyjnym:

1. Idź do **MGW Hide Content Panel**
2. **Dodawanie nowego tagu:**
   - Wypełnij pole "Custom Message (HTML allowed)"
   - Pozostaw puste aby używać globalnej wiadomości
3. **Edycja istniejącego tagu:**
   - Kliknij "Edit" przy tagu
   - Dodaj/edytuj Custom Message

### Wskazówki:

- **HTML dozwolony:** Możesz używać pełnego HTML i CSS
- **Responsywność:** Pamiętaj o urządzeniach mobilnych
- **Bezpieczeństwo:** Unikaj JavaScript w wiadomościach
- **Testowanie:** Zawsze przetestuj na różnych grupach użytkowników

## Kompatybilność

### Motywy MyBB:
- Wiadomości dziedziczą style z motywu
- Można używać klas CSS motywu
- Bootstrap i Font Awesome jeśli dostępne

### Istniejące instalacje:
- Pełna kompatybilność wsteczna
- Istniejące tagi działają bez zmian
- Można stopniowo dodawać custom messages

## Rozwiązywanie problemów

### Custom message nie wyświetla się:
1. Sprawdź czy pole nie jest puste
2. Sprawdź poprawność HTML
3. Sprawdź uprawnienia użytkownika
4. Wyczyść cache MyBB

### Problemy z formatowaniem:
1. Waliduj HTML w walidatorze online
2. Sprawdź konflikty z CSS motywu
3. Testuj na różnych przeglądarkach

### Aktualizacja schematu nie działa:
1. Sprawdź uprawnienia do bazy danych
2. Sprawdz logi błędów MySQL
3. Uruchom jako administrator MyBB

## API dla developerów

Jeśli tworzysz własne motywy lub pluginy:

```php
// Pobierz tag z custom message
$tag = $db->fetch_array($db->simple_select("mgw_hide_tags", "*", "tag_name = 'vip'"));

// Sprawdź czy ma custom message
if(!empty($tag['custom_message'])) {
    $message = $tag['custom_message']; // Użyj custom
} else {
    $message = $mybb->settings['mgw_hide_show_message']; // Użyj globalnej
}
```

---

**Podsumowanie:** Nowa funkcjonalność custom messages pozwala na pełną personalizację wiadomości dla każdego tagu, co znacznie zwiększa możliwości wizualne i funkcjonalne pluginu. 