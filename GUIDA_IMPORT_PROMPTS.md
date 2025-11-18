# Guida Sistema Import Prompts - Epic Prompts

**Data:** 18 Novembre 2025
**Versione:** 1.0
**Branch:** claude/analyze-wordpress-site-016tvJmtMSeJcPt6NjEZvCwG

---

## 📋 Panoramica

Questo sistema completo permette di:
1. **Generare** automaticamente 100 prompt di alta qualità in formato JSON usando AI
2. **Importare** i prompt nel sito WordPress Epic Prompts da un file JSON remoto
3. **Gestire** l'importazione con diverse modalità (skip, update, fresh)
4. **Validare** la struttura JSON prima dell'importazione

---

## 🎯 Componenti del Sistema

### 1. Prompt Generator (`PROMPT_GENERATORE_JSON.md`)
Prompt AI che genera 100 prompt in formato JSON strutturato.

### 2. WordPress Plugin (`epic-prompts-importer/`)
Plugin completo per importazione JSON con interfaccia admin.

**File del Plugin:**
```
epic-prompts-importer/
├── epic-prompts-importer.php    # File principale plugin
├── assets/
│   ├── admin.css                # Stili interfaccia admin
│   └── admin.js                 # JavaScript AJAX handlers
└── example-prompts.json         # Esempio JSON con 5 prompts
```

---

## 🚀 Come Usare il Sistema

### Passo 1: Generare il JSON con AI

#### 1.1. Scegli la piattaforma AI
Usa **ChatGPT-4**, **Claude Sonnet 3.5** o **Gemini Pro** per generare i prompts.

#### 1.2. Carica il prompt
Apri il file `PROMPT_GENERATORE_JSON.md` e copia l'intero contenuto nella tua piattaforma AI.

#### 1.3. Genera il JSON
L'AI genererà un file JSON completo con 100 prompts. Esempio output:

```json
{
  "version": "1.0",
  "generated_date": "2025-11-18",
  "total_prompts": 100,
  "prompts": [
    {
      "title": "Professional Email Writer for Customer Service",
      "prompt_text": "You are a professional email writer...",
      "description": "This prompt helps customer service teams...",
      "platform": "ChatGPT",
      "prompt_type": "Text Generation",
      "category": "Business & Marketing",
      "tags": ["email", "customer-service", "professional-writing"],
      "difficulty": "beginner",
      "estimated_tokens": 180
    },
    // ... altri 99 prompts
  ]
}
```

#### 1.4. Salva il JSON
- Copia l'output JSON completo
- Salvalo come file `.json` (es: `epic-prompts-100.json`)
- Caricalo su un server accessibile via URL (GitHub Gist, Dropbox, server web, ecc.)

**Opzioni per hosting JSON:**

1. **GitHub Gist** (Consigliato)
   - Vai su https://gist.github.com/
   - Crea un nuovo Gist
   - Incolla il JSON
   - Usa il pulsante "Raw" per ottenere l'URL diretto
   - Esempio: `https://gist.githubusercontent.com/username/abc123/raw/prompts.json`

2. **Dropbox**
   - Carica il file JSON
   - Genera link di condivisione
   - Cambia `dl=0` in `dl=1` nell'URL
   - Esempio: `https://www.dropbox.com/s/abc123/prompts.json?dl=1`

3. **GitHub Repository**
   - Commit il JSON nel tuo repo
   - Usa l'URL raw
   - Esempio: `https://raw.githubusercontent.com/user/repo/main/prompts.json`

4. **Server Web Proprio**
   - Carica su `https://tuosito.com/prompts.json`
   - Assicurati che il file sia pubblicamente accessibile

---

### Passo 2: Installare il Plugin

#### 2.1. Upload del Plugin

**Metodo 1: Via FTP/SFTP**
```bash
# Carica la cartella epic-prompts-importer in:
wp-content/plugins/epic-prompts-importer/
```

**Metodo 2: Via ZIP**
```bash
# Comprimi la cartella
cd /path/to/epic-prompts-importer
zip -r epic-prompts-importer.zip .

# Poi carica via WordPress Admin:
# Dashboard > Plugin > Aggiungi Nuovo > Carica Plugin
```

#### 2.2. Attivare il Plugin

1. Vai su **Dashboard > Plugin**
2. Trova "Epic Prompts Importer"
3. Clicca su **Attiva**

---

### Passo 3: Importare i Prompts

#### 3.1. Accedi alla Pagina Import

Vai su: **Dashboard > AI Prompts > Import JSON**

#### 3.2. Inserisci l'URL del JSON

Nella sezione **JSON Source**, incolla l'URL del tuo file JSON:

```
https://gist.githubusercontent.com/username/abc123/raw/prompts.json
```

#### 3.3. Scegli la Modalità di Import

**Modalità disponibili:**

| Modalità | Descrizione | Quando usarla |
|----------|-------------|---------------|
| **Skip Duplicates** | Importa solo prompt nuovi. Se esiste già un prompt con lo stesso titolo, lo salta. | Prima importazione o aggiunta di nuovi prompts |
| **Update Existing** | Importa tutti i prompts. Aggiorna i prompts esistenti con i nuovi dati. | Aggiornamento prompts esistenti |
| **Fresh Import** | ⚠️ **ATTENZIONE:** Cancella TUTTI i prompts esistenti e importa da zero. | Reset completo del database |

#### 3.4. Valida il JSON

1. Clicca su **🔍 Validate JSON**
2. Il sistema controllerà:
   - URL accessibile
   - Struttura JSON valida
   - Campi obbligatori presenti
   - Formato corretto

3. Vedrai i risultati della validazione:
   - ✅ **Success:** JSON valido, mostra statistiche
   - ❌ **Error:** JSON non valido, mostra errori

**Esempio Output Validazione:**
```
✅ Validation Successful!
JSON structure is valid and ready for import.

Total Prompts: 100
Platforms: 12
Prompt Types: 8
Categories: 15
```

#### 3.5. Avvia l'Importazione

1. Clicca su **🚀 Start Import**
2. Vedrai la progress bar in tempo reale
3. Al termine vedrai le statistiche:

```
🎉 Import Successful!
All prompts imported successfully.

Imported: 95
Updated: 0
Skipped: 5
Errors: 0
```

#### 3.6. Verifica i Prompts Importati

Clicca su **View All Prompts** o vai su **Dashboard > AI Prompts** per vedere tutti i prompts importati.

---

## 📊 Struttura JSON Dettagliata

### Schema Completo

```json
{
  "version": "1.0",              // Versione schema (string, obbligatorio)
  "generated_date": "2025-11-18", // Data generazione (string, opzionale)
  "total_prompts": 100,           // Numero totale prompts (int, obbligatorio)
  "prompts": [                    // Array di prompts (array, obbligatorio)
    {
      // CAMPI OBBLIGATORI
      "title": "string",          // Titolo prompt (1-200 caratteri)
      "prompt_text": "string",    // Testo del prompt (min 50 caratteri)
      "platform": "string",       // Piattaforma AI
      "prompt_type": "string",    // Tipo di prompt
      "category": "string",       // Categoria principale

      // CAMPI OPZIONALI
      "description": "string",    // Descrizione aggiuntiva
      "tags": ["tag1", "tag2"],   // Array di tag
      "difficulty": "string",     // beginner|intermediate|advanced
      "estimated_tokens": 150     // Numero token stimati
    }
  ]
}
```

### Campi Dettagliati

#### `title` (string, obbligatorio)
- **Lunghezza:** 10-200 caratteri
- **Formato:** Descrittivo e chiaro
- **Esempi:**
  - ✅ "Professional Email Writer for Customer Service"
  - ✅ "Python Function Documentation Generator"
  - ❌ "Prompt 1" (troppo generico)
  - ❌ "Email" (troppo corto)

#### `prompt_text` (string, obbligatorio)
- **Lunghezza:** Minimo 50 caratteri
- **Formato:** Testo completo del prompt pronto all'uso
- **Suggerimenti:**
  - Include istruzioni chiare
  - Usa placeholder per variabili: `[DESCRIVI PROBLEMA]`
  - Specifica formato output desiderato
  - Aggiungi esempi se utile

#### `description` (string, opzionale)
- **Lunghezza:** 50-500 caratteri
- **Contenuto:**
  - Come usare il prompt
  - Casi d'uso
  - Tips aggiuntivi
  - Variazioni possibili

#### `platform` (string, obbligatorio)
- **Valori accettati:** Nome della piattaforma AI
- **Piattaforme supportate:**

**Text/Chat AI:**
- ChatGPT, ChatGPT-4, Claude, Claude Sonnet, Gemini, Gemini Pro
- Copilot, Perplexity, Llama, Mistral, Grok

**Image Generation:**
- Midjourney, DALL-E, DALL-E 3, Stable Diffusion, Leonardo AI
- Adobe Firefly, Ideogram, Flux

**Video Generation:**
- Runway, Sora, Pika, Synthesia, HeyGen

**Code Generation:**
- GitHub Copilot, Cursor, Replit AI, Tabnine, CodeWhisperer

**Audio/Music:**
- ElevenLabs, Suno, Udio, Mubert

#### `prompt_type` (string, obbligatorio)
- **Valori accettati:**
  - Text Generation
  - Image Generation
  - Code Generation
  - Video Generation
  - Audio Generation
  - Data Analysis
  - Translation
  - Summarization
  - Creative Writing
  - Technical Writing
  - Strategic Planning
  - Question Answering
  - Conversation
  - Research
  - Education

#### `category` (string, obbligatorio)
- **Valori accettati:**
  - Content Writing & Copywriting
  - Business & Marketing
  - Programming & Development
  - Data & Analytics
  - Creative & Design
  - Social Media & Influencer
  - E-commerce & Product
  - Education & Learning
  - Health & Wellness
  - Travel & Lifestyle
  - Finance & Investment
  - Legal & Compliance
  - HR & Recruitment
  - Real Estate
  - Entertainment & Gaming
  - Science & Research
  - News & Journalism
  - Personal Development
  - Productivity & Tools
  - Other

#### `tags` (array, opzionale)
- **Formato:** Array di stringhe
- **Lunghezza:** 2-10 tag consigliati
- **Formato tag:** lowercase, separati da trattino per parole multiple
- **Esempi:**
  ```json
  ["email", "customer-service", "business-communication"]
  ["python", "documentation", "code-quality"]
  ["social-media", "marketing-strategy", "content-calendar"]
  ```

#### `difficulty` (string, opzionale)
- **Valori accettati:**
  - `"beginner"` - Facile da usare, non richiede esperienza
  - `"intermediate"` - Richiede conoscenza base della piattaforma
  - `"advanced"` - Richiede esperienza avanzata o tecnica

#### `estimated_tokens` (integer, opzionale)
- **Valore:** Numero stimato di token utilizzati
- **Range:** Tipicamente 50-1000
- **Utilità:** Aiuta utenti a stimare costi API

---

## 🔧 Funzionalità del Plugin

### Validazione JSON

Il plugin valida automaticamente:

1. **Struttura Base**
   - Campo `version` presente
   - Campo `total_prompts` presente e numero
   - Campo `prompts` è array

2. **Validazione Prompts**
   - Ogni prompt ha titolo
   - Ogni prompt ha prompt_text
   - Ogni prompt ha platform
   - Ogni prompt ha prompt_type
   - Ogni prompt ha category

3. **Consistenza Dati**
   - `total_prompts` corrisponde al numero di prompts nell'array
   - Nessun campo obbligatorio vuoto
   - Formato JSON valido

### Modalità di Importazione

#### Skip Duplicates (Consigliato per prima importazione)

```php
// Logica:
foreach ($prompts as $prompt) {
    if (prompt_exists($prompt['title'])) {
        skip(); // Salta questo prompt
    } else {
        import_new(); // Importa come nuovo
    }
}
```

**Vantaggi:**
- Sicuro, non sovrascrive dati esistenti
- Veloce per grandi dataset
- Ideale per aggiungere nuovi prompts

**Uso:**
- Prima importazione del database
- Aggiunta periodica di nuovi prompts
- Import incrementale

#### Update Existing

```php
// Logica:
foreach ($prompts as $prompt) {
    if (prompt_exists($prompt['title'])) {
        update_existing(); // Aggiorna con nuovi dati
    } else {
        import_new(); // Importa come nuovo
    }
}
```

**Vantaggi:**
- Mantiene ID dei prompts esistenti
- Aggiorna contenuto migliorato
- Preserva views, reactions, saves

**Uso:**
- Aggiornamento prompts esistenti
- Correzione errori
- Miglioramento qualità prompts

#### Fresh Import (⚠️ Attenzione)

```php
// Logica:
delete_all_prompts(); // Cancella TUTTO
foreach ($prompts as $prompt) {
    import_new(); // Importa tutti da zero
}
```

**Vantaggi:**
- Database pulito
- Nessun duplicato garantito
- Reset completo

**Svantaggi:**
- ⚠️ **PERDE TUTTI I DATI:** views, reactions, saves, comments
- ⚠️ **NON REVERSIBILE**
- ⚠️ **Rompe link esistenti**

**Uso:**
- Solo per reset completo volontario
- Testing e development
- Passaggio a nuovo dataset completamente diverso

### Creazione Automatica Tassonomie

Il plugin crea automaticamente:

1. **Platforms (`ai_platform`)**
   - Se "ChatGPT" non esiste, lo crea
   - Associa il prompt alla piattaforma corretta

2. **Prompt Types (`prompt_type`)**
   - Crea tipo se non esiste
   - Es: "Text Generation", "Image Generation"

3. **Categories (`prompt_category`)**
   - Crea categoria se non esiste
   - Es: "Business & Marketing", "Programming"

4. **Tags**
   - Crea ogni tag se non esiste
   - Associa tutti i tag al prompt

### Inizializzazione Meta Data

Per ogni prompt importato, il plugin inizializza:

```php
// Contatori
update_post_meta($prompt_id, 'views_count', 0);
update_post_meta($prompt_id, 'saves_count', 0);
update_post_meta($prompt_id, 'copies_count', 0);
update_post_meta($prompt_id, 'shares_count', 0);

// Shares per piattaforma
update_post_meta($prompt_id, 'shares_twitter', 0);
update_post_meta($prompt_id, 'shares_facebook', 0);
update_post_meta($prompt_id, 'shares_linkedin', 0);
update_post_meta($prompt_id, 'shares_whatsapp', 0);
update_post_meta($prompt_id, 'shares_telegram', 0);
update_post_meta($prompt_id, 'shares_email', 0);

// Reactions
update_post_meta($prompt_id, 'reaction_fire', 0);
update_post_meta($prompt_id, 'reaction_heart', 0);
update_post_meta($prompt_id, 'reaction_star', 0);
update_post_meta($prompt_id, 'reaction_rocket', 0);
update_post_meta($prompt_id, 'reaction_clap', 0);

// Rating
update_post_meta($prompt_id, 'average_rating', 0);
update_post_meta($prompt_id, 'rating_count', 0);

// Campi custom
update_post_meta($prompt_id, 'difficulty', $data['difficulty'] ?? 'intermediate');
update_post_meta($prompt_id, 'estimated_tokens', $data['estimated_tokens'] ?? 0);
```

---

## 🎨 Interfaccia Admin

### Layout

La pagina admin è divisa in sezioni:

#### Header
- Gradiente colorato (indigo → pink)
- Titolo e descrizione

#### Import Form
- Input URL JSON
- Radio buttons per modalità import
- Descrizione dinamica modalità selezionata
- Pulsanti Validate e Import

#### Progress Section
- Barra progresso animata
- Percentuale completamento
- Messaggio stato corrente

#### Results Section
- Alert success/error
- Statistiche import (imported, updated, skipped, errors)
- Link "View All Prompts"

#### Example & Tips
- Esempio JSON
- Link file example
- Tips per creare JSON validi

### Design System

**Colori:**
- Primary: `#6366F1` (Indigo)
- Secondary: `#EC4899` (Pink)
- Success: `#10B981` (Green)
- Error: `#EF4444` (Red)
- Warning: `#F59E0B` (Amber)
- Info: `#3B82F6` (Blue)

**Typography:**
- Font system stack
- Dimensioni responsive
- Font weight 600-700 per headings

**Spacing:**
- Sistema 0.25rem base
- Padding cards: 2rem
- Gap grid: 1rem-1.5rem

---

## 🐛 Troubleshooting

### Errore: "Failed to fetch JSON from URL"

**Possibili cause:**
1. URL non accessibile pubblicamente
2. File non esiste
3. CORS issue
4. Server down

**Soluzioni:**
```bash
# Test URL direttamente
curl -I https://your-url.com/prompts.json

# Verifica headers
curl -H "Accept: application/json" https://your-url.com/prompts.json

# Test in browser
# Apri l'URL e dovresti vedere il JSON puro
```

### Errore: "Invalid JSON structure"

**Possibili cause:**
1. Sintassi JSON errata
2. Campi obbligatori mancanti
3. Formato dati errato

**Soluzioni:**
```bash
# Valida JSON online
# https://jsonlint.com/

# Valida localmente
cat prompts.json | jq .

# Controlla campi obbligatori
jq '.prompts[0] | keys' prompts.json
```

### Errore: "Some prompts failed to import"

**Possibili cause:**
1. Titoli duplicati
2. Dati prompt invalidi
3. Tassonomia non valida

**Soluzioni:**
1. Controlla i messaggi di errore specifici
2. Verifica titoli unici
3. Controlla spelling platform/type/category

### Warning: "X prompts skipped"

**Cause (modalità Skip):**
- Prompts con stesso titolo già esistono

**Soluzioni:**
- Normale in modalità "Skip Duplicates"
- Usa "Update Existing" per aggiornare
- Cambia titoli se vuoi import come nuovi

### Errore: "Permission denied"

**Cause:**
- User non ha permesso `manage_options`
- Nonce non valido

**Soluzioni:**
```php
// Verifica capacità utente
if (current_user_can('manage_options')) {
    // OK
}

// Rigenera nonce
wp_nonce_field('epic_prompts_nonce');
```

### Import molto lento

**Cause:**
- File JSON molto grande (>1000 prompts)
- Server lento
- Timeout PHP

**Soluzioni:**
```php
// Aumenta timeout in wp-config.php
set_time_limit(300); // 5 minuti

// Oppure in .htaccess
php_value max_execution_time 300

// Dividi JSON in batch più piccoli
// Es: 5 file da 20 prompts invece di 1 file da 100
```

---

## 📈 Best Practices

### Generazione JSON

1. **Qualità sui numeri**
   - Meglio 50 prompts eccellenti che 100 mediocri
   - Testa ogni prompt prima di includerlo

2. **Diversificazione**
   - Varia piattaforme e categorie
   - Mix difficoltà (40% beginner, 40% intermediate, 20% advanced)
   - Include casi d'uso diversi

3. **Descrizioni complete**
   - Spiega quando usare il prompt
   - Aggiungi esempi di output
   - Include tips e variazioni

4. **Tag efficaci**
   - Usa 3-7 tag per prompt
   - Tag specifici > tag generici
   - Usa naming consistente

5. **Validazione pre-import**
   - Valida JSON con tool online
   - Test import su staging prima di production
   - Backup database prima di Fresh Import

### Uso Plugin

1. **Prima importazione**
   - Usa modalità "Skip Duplicates"
   - Valida sempre prima di importare
   - Inizia con file piccolo di test (5-10 prompts)

2. **Aggiornamenti**
   - Usa "Update Existing" per correzioni
   - Backup database prima di update massivi
   - Monitora statistiche import

3. **Testing**
   - Test su ambiente staging
   - Verifica prompts importati manualmente
   - Controlla tassonomie create correttamente

4. **Manutenzione**
   - Importa nuovi prompts regolarmente
   - Aggiorna prompts esistenti con feedback utenti
   - Rimuovi prompts low-performing

---

## 🔐 Sicurezza

### Nonce Verification

Tutte le richieste AJAX verificano nonce:

```php
check_ajax_referer('epic_prompts_importer_nonce', 'nonce');
```

### Capability Check

Solo admin possono importare:

```php
if (!current_user_can('manage_options')) {
    wp_send_json_error(['message' => 'Insufficient permissions']);
}
```

### Sanitizzazione Input

```php
$json_url = esc_url_raw($_POST['json_url']);
$import_mode = sanitize_text_field($_POST['import_mode']);
```

### Validazione Remote URL

```php
// Verifica response code
$response_code = wp_remote_retrieve_response_code($response);
if ($response_code !== 200) {
    // Error
}

// Valida JSON
$data = json_decode($body, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    // Error
}
```

---

## 📚 Esempi Pratici

### Esempio 1: Import Iniziale

```
1. Genera JSON con AI usando PROMPT_GENERATORE_JSON.md
2. Salva come epic-prompts-100.json
3. Carica su GitHub Gist
4. Copia URL raw: https://gist.githubusercontent.com/.../prompts.json
5. Vai su WP Admin > AI Prompts > Import JSON
6. Incolla URL
7. Seleziona "Skip Duplicates"
8. Click "Validate JSON"
9. Verifica statistiche: 100 prompts, 12 platforms, ecc.
10. Click "Start Import"
11. Aspetta completamento
12. Risultato: 100 imported, 0 skipped, 0 errors
13. Click "View All Prompts"
```

### Esempio 2: Aggiornamento Prompts

```
1. Modifica JSON esistente con prompts migliorati
2. Mantieni stesso titolo per prompts da aggiornare
3. Carica nuovo JSON
4. Seleziona "Update Existing"
5. Valida e importa
6. Risultato: 20 imported (nuovi), 80 updated, 0 skipped
```

### Esempio 3: Reset Completo

```
⚠️ ATTENZIONE: Cancella tutti i prompts esistenti

1. Backup database prima!
2. Genera nuovo JSON completo
3. Seleziona "Fresh Import"
4. Conferma warning popup
5. Risultato: Tutti prompts sostituiti
```

---

## 🧪 Testing

### Test Locale

```bash
# 1. Crea JSON di test
cat > test-prompts.json << 'EOF'
{
  "version": "1.0",
  "total_prompts": 3,
  "prompts": [
    {
      "title": "Test Prompt 1",
      "prompt_text": "This is a test prompt for validation...",
      "platform": "ChatGPT",
      "prompt_type": "Text Generation",
      "category": "Testing"
    },
    {
      "title": "Test Prompt 2",
      "prompt_text": "Another test prompt...",
      "platform": "Claude",
      "prompt_type": "Code Generation",
      "category": "Programming & Development"
    },
    {
      "title": "Test Prompt 3",
      "prompt_text": "Third test prompt...",
      "platform": "Midjourney",
      "prompt_type": "Image Generation",
      "category": "Creative & Design"
    }
  ]
}
EOF

# 2. Valida JSON
jq . test-prompts.json

# 3. Serve localmente (se hai Python)
python3 -m http.server 8000

# 4. URL: http://localhost:8000/test-prompts.json
```

### Test Checklist

- [ ] JSON valido (jsonlint.com)
- [ ] Tutti campi obbligatori presenti
- [ ] `total_prompts` corrisponde ad array length
- [ ] URL accessibile pubblicamente
- [ ] Test import in staging
- [ ] Verifica tassonomie create
- [ ] Verifica meta data inizializzati
- [ ] Check prompts visibili in frontend
- [ ] Test search e filters
- [ ] Backup database

---

## 🎓 FAQ

**Q: Posso importare più di 100 prompts?**
A: Sì, non c'è limite tecnico. Tuttavia per file molto grandi (>500 prompts) considera di aumentare il timeout PHP.

**Q: Cosa succede se il JSON ha errori?**
A: La validazione li rileverà prima dell'import. L'import non partirà finché non sono corretti.

**Q: Posso importare da URL privati?**
A: No, l'URL deve essere pubblicamente accessibile. Usa GitHub Gist privato o proteggi con token.

**Q: I prompts importati sono pubblicati subito?**
A: Sì, sono pubblicati con stato `publish`. Puoi modificare il plugin per usare `draft`.

**Q: Posso reimportare lo stesso JSON?**
A: Sì. Con "Skip Duplicates" non crea duplicati. Con "Update" aggiorna i dati.

**Q: Come elimino prompts importati per errore?**
A: Vai su AI Prompts > All Prompts, seleziona e cancella manualmente. O usa "Fresh Import" con nuovo JSON.

**Q: Il plugin supporta import da file locale?**
A: Attualmente solo da URL remoto. Puoi estendere per supportare upload file.

**Q: Posso schedulare import automatici?**
A: Non di default. Puoi aggiungere cron job WP per import periodico.

**Q: Supporta import di immagini result_image?**
A: Non in questa versione. Il plugin importa solo dati testuali.

**Q: Come miglioro performance per file grandi?**
A: Aumenta `max_execution_time`, usa batch processing, o dividi in file più piccoli.

---

## 🔄 Aggiornamenti Futuri

### Roadmap

**v1.1**
- [ ] Import da file upload locale
- [ ] Batch processing per file grandi
- [ ] Progress bar real-time più accurata
- [ ] Export prompts in JSON

**v1.2**
- [ ] Cron job per import schedulato
- [ ] Import immagini da URL
- [ ] Mapping custom fields
- [ ] Import history con rollback

**v1.3**
- [ ] Import da CSV
- [ ] Import da Google Sheets
- [ ] Validazione avanzata con AI
- [ ] Duplicate detection intelligente

---

## 👥 Credits

**Sviluppatore:** Claude AI Assistant
**Data Implementazione:** 18 Novembre 2025
**Versione Plugin:** 1.0
**Versione Documentazione:** 1.0
**Progetto:** Epic Prompts
**Repository:** marsan456-ai/epic-prompts

---

## 📞 Supporto

Per problemi o domande:

1. Controlla questa documentazione
2. Verifica sezione Troubleshooting
3. Test con `example-prompts.json` incluso
4. Verifica logs WordPress (Debug mode)
5. Apri issue su GitHub repository

---

**Fine Documentazione Sistema Import**
