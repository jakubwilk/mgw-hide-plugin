# Szablony i Style - MGW Hide Content

## Przegląd szablonów

Plugin MGW Hide Content wykorzystuje system szablonów MyBB do renderowania ukrytej treści. Wszystkie szablony można edytować z poziomu panelu administracyjnego.

### Dostępne szablony

1. **mgw_hide_message** - Komunikat dla zalogowanych użytkowników
2. **mgw_hide_message_guest** - Komunikat dla gości  
3. **mgw_hide_visible_content** - Wrapper dla widocznej treści

## Przykłady szablonów

### mgw_hide_message (domyślny)

```html
<div class="mgw_hide_message">
    <div class="mgw_hide_icon">🔒</div>
    <div class="mgw_hide_content">
        <strong>{$lang->mgw_hide_content_hidden}</strong>
        <p>{$message}</p>
        {$additional_info}
    </div>
</div>
```

### mgw_hide_message_guest (domyślny)

```html
<div class="mgw_hide_message mgw_hide_guest">
    <div class="mgw_hide_icon">🔒</div>
    <div class="mgw_hide_content">
        <strong>{$lang->mgw_hide_content_hidden}</strong>
        <p>{$message}</p>
        <p class="mgw_hide_login_prompt">{$lang->mgw_hide_login_required}</p>
    </div>
</div>
```

### mgw_hide_visible_content (domyślny)

```html
<div class="mgw_hide_visible">
    {$content}
</div>
```

## Zmienne dostępne w szablonach

### Wszystkie szablony
- `{$lang->mgw_hide_content_hidden}` - Etykieta "Hidden Content"
- `{$lang->mgw_hide_login_required}` - "Please login to view this content"

### mgw_hide_message i mgw_hide_message_guest
- `{$message}` - Komunikat skonfigurowany w ustawieniach pluginu
- `{$additional_info}` - Dodatkowe informacje (obecnie nieużywane, zarezerwowane)

### mgw_hide_visible_content
- `{$content}` - Rzeczywista ukryta treść do wyświetlenia

## Przykłady niestandardowych szablonów

### Minimalny szablon

```html
<div class="simple-hide">⚠️ {$message}</div>
```

### Szablon z ikoną Bootstrap

```html
<div class="alert alert-warning d-flex align-items-center">
    <i class="fas fa-lock me-2"></i>
    <div>
        <strong>Treść ukryta</strong><br>
        {$message}
    </div>
</div>
```

### Szablon z przyciskiem logowania

```html
<div class="mgw_hide_message mgw_hide_guest">
    <div class="mgw_hide_header">
        <span class="mgw_hide_icon">🔐</span>
        <strong>{$lang->mgw_hide_content_hidden}</strong>
    </div>
    <p>{$message}</p>
    <div class="mgw_hide_actions">
        <a href="member.php?action=login" class="btn-login">Zaloguj się</a>
        <a href="member.php?action=register" class="btn-register">Zarejestruj się</a>
    </div>
</div>
```

### Szablon z animacją

```html
<div class="mgw_hide_animated">
    <div class="mgw_hide_pulse">🔒</div>
    <div class="mgw_hide_text">
        <strong>{$lang->mgw_hide_content_hidden}</strong>
        <p>{$message}</p>
    </div>
</div>
```

## Style CSS

### Domyślne style

```css
/* Podstawowy komunikat ukrytej treści */
.mgw_hide_message {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-left: 4px solid #007bff;
    padding: 15px;
    margin: 15px 0;
    border-radius: 5px;
    display: flex;
    align-items: flex-start;
}

/* Specjalny styl dla gości */
.mgw_hide_message.mgw_hide_guest {
    border-left-color: #ffc107;
}

/* Ikona */
.mgw_hide_icon {
    font-size: 20px;
    margin-right: 10px;
    margin-top: 2px;
}

/* Treść komunikatu */
.mgw_hide_content {
    flex: 1;
}

.mgw_hide_content strong {
    color: #495057;
    font-weight: 600;
}

.mgw_hide_content p {
    margin: 5px 0 0 0;
    color: #6c757d;
}

/* Prompt logowania */
.mgw_hide_login_prompt {
    font-style: italic;
    color: #856404 !important;
}
```

### Przykłady niestandardowych stylów

#### Styl ciemny

```css
.mgw_hide_message {
    background: #2c3e50;
    border: 1px solid #34495e;
    border-left: 4px solid #3498db;
    color: #ecf0f1;
}

.mgw_hide_content strong {
    color: #ecf0f1;
}

.mgw_hide_content p {
    color: #bdc3c7;
}
```

#### Styl gradientowy

```css
.mgw_hide_message {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.mgw_hide_icon {
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}
```

#### Styl z animacją

```css
.mgw_hide_animated {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}

.mgw_hide_pulse {
    font-size: 2em;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 0.5; transform: scale(1); }
    50% { opacity: 1; transform: scale(1.1); }
    100% { opacity: 0.5; transform: scale(1); }
}
```

#### Styl karty

```css
.mgw_hide_message {
    background: white;
    border: none;
    border-radius: 15px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
    padding: 25px;
    position: relative;
    overflow: hidden;
}

.mgw_hide_message::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1);
}
```

## Responsywność

### Podstawowe media queries

```css
/* Tablety */
@media (max-width: 768px) {
    .mgw_hide_message {
        padding: 12px;
        margin: 10px 0;
        flex-direction: column;
        text-align: center;
    }
    
    .mgw_hide_icon {
        margin: 0 0 10px 0;
        align-self: center;
    }
}

/* Telefony */
@media (max-width: 480px) {
    .mgw_hide_message {
        padding: 10px;
        margin: 8px 0;
        border-radius: 8px;
    }
    
    .mgw_hide_icon {
        font-size: 16px;
    }
}
```

## Integracja z frameworkami CSS

### Bootstrap 5

```html
<div class="alert alert-info d-flex align-items-start">
    <i class="bi bi-lock-fill me-3 fs-4"></i>
    <div>
        <h6 class="alert-heading mb-1">{$lang->mgw_hide_content_hidden}</h6>
        <p class="mb-0">{$message}</p>
    </div>
</div>
```

### Tailwind CSS

```html
<div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">{$lang->mgw_hide_content_hidden}</h3>
            <p class="mt-1 text-sm text-blue-700">{$message}</p>
        </div>
    </div>
</div>
```

## Najlepsze praktyki

### 1. Semantyka HTML
- Używaj odpowiednich tagów HTML5
- Dodawaj atrybuty `aria-*` dla dostępności
- Używaj nagłówków w odpowiedniej hierarchii

### 2. Wydajność CSS
- Unikaj nadmiernie zagnieżdżonych selektorów
- Używaj CSS Grid/Flexbox zamiast float
- Optymalizuj animacje (prefer `transform` i `opacity`)

### 3. Dostępność
- Zapewnij odpowiedni kontrast kolorów
- Dodaj focus states dla elementów interaktywnych
- Używaj `screen reader` friendly tekstu

### 4. Kompatybilność
- Testuj w różnych przeglądarkach
- Używaj fallbacków dla starszych przeglądarek
- Sprawdzaj na różnych rozmiarach ekranów

## Debugowanie

### Problemy z szablonami
1. Sprawdź składnię PHP w zmiennych
2. Upewnij się, że zmienne są poprawnie zapisane
3. Sprawdź cache MyBB

### Problemy z CSS
1. Użyj narzędzi deweloperskich przeglądarki
2. Sprawdź priorytet selektorów CSS
3. Wyczyść cache przeglądarki

### Logowanie błędów
Plugin zapisuje błędy w logach MyBB. Sprawdź:
- Logi PHP
- Logi MyBB
- Konsola przeglądarki (dla CSS/JS) 