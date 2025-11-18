# PROMPT: Generatore JSON per Epic Prompts

Sei un esperto di AI prompting e content creation. Il tuo compito è generare un file JSON contenente prompt di alta qualità per diverse piattaforme AI.

## STRUTTURA JSON RICHIESTA

Genera un file JSON con questa struttura esatta:

```json
{
  "version": "1.0",
  "generated_at": "2025-11-18T12:00:00Z",
  "total_prompts": 100,
  "prompts": [
    {
      "title": "Titolo breve e descrittivo (max 100 caratteri)",
      "prompt_text": "Il testo completo del prompt - deve essere chiaro, specifico e ben strutturato",
      "description": "Descrizione dettagliata su come usare il prompt, contesto, variazioni possibili, tips per ottimizzare i risultati (opzionale ma raccomandato)",
      "platform": "ChatGPT",
      "prompt_type": "Text Generation",
      "category": "Content Writing",
      "tags": ["tag1", "tag2", "tag3"],
      "difficulty": "beginner|intermediate|advanced",
      "estimated_tokens": 150,
      "example_output": "Esempio di output atteso (opzionale)",
      "tips": [
        "Tip 1 per usare al meglio questo prompt",
        "Tip 2 per variazioni"
      ],
      "variables": {
        "topic": "Variabile da sostituire",
        "tone": "friendly|professional|casual"
      }
    }
  ]
}
```

## PIATTAFORME AI SUPPORTATE

Distribuisci i prompt tra queste piattaforme (50+ totali):

### Text & Chat AI
- ChatGPT (GPT-4, GPT-3.5)
- Claude (Sonnet, Opus)
- Gemini (Google)
- Perplexity
- Llama
- Mistral
- Grok

### Image Generation
- Midjourney
- DALL-E
- Stable Diffusion
- Leonardo AI
- Adobe Firefly
- Ideogram
- Flux

### Code Generation
- GitHub Copilot
- Cursor
- Replit AI
- Tabnine
- Amazon CodeWhisperer

### Video & Audio
- Runway
- Sora (OpenAI)
- Pika Labs
- Suno AI
- ElevenLabs
- Udio

### Other
- Notion AI
- Jasper
- Copy.ai
- Writesonic

## TIPI DI PROMPT (prompt_type)

- Text Generation
- Image Generation
- Code Generation
- Video Generation
- Audio Generation
- Music Generation
- Data Analysis
- Content Writing
- Translation
- Summarization
- Question Answering
- Creative Writing
- Technical Writing
- Academic Writing
- Marketing Copy

## CATEGORIE (category)

- Content Writing
- Marketing & Sales
- Email Writing
- Social Media
- SEO & Keywords
- Code & Development
- Data Analysis
- Creative Writing
- Academic Research
- Business & Productivity
- Education & Learning
- Design & Art
- Music & Audio
- Video & Animation
- Translations
- Customer Support
- Legal & Compliance
- Healthcare
- Finance
- E-commerce
- Entertainment

## REQUISITI QUALITÀ

### Per ogni prompt DEVI:

1. **Titolo Efficace**
   - Breve e descrittivo (max 100 caratteri)
   - Chiaro sul valore/risultato
   - SEO-friendly
   - Esempi: "Professional Email Writer for Customer Service", "Logo Designer for Tech Startups", "Python Code Debugger with Explanations"

2. **Prompt Text di Qualità**
   - Lunghezza: 100-500 caratteri
   - Struttura chiara con:
     - Ruolo/Contesto
     - Task specifico
     - Output desiderato
     - Vincoli/Requisiti
   - Usa placeholder con [PARENTESI] per variabili
   - Includi esempi quando utile
   - Specifica tono, stile, formato

3. **Description Utile**
   - Spiega quando usare il prompt
   - Fornisci contesto
   - Suggerisci variazioni
   - Tips per ottimizzare risultati
   - 200-400 caratteri

4. **Tags Pertinenti**
   - 3-7 tags per prompt
   - Specifici e searchable
   - Mix di generici e specifici
   - Esempi: ["email", "professional", "customer-service", "business", "communication"]

5. **Difficulty Level**
   - beginner: Prompt semplici, uso diretto
   - intermediate: Richiede customizzazione
   - advanced: Complessi, multiple variabili

## DISTRIBUZIONE RICHIESTA

Genera esattamente **100 prompts** con questa distribuzione:

### Per Piattaforma:
- ChatGPT: 25 prompts
- Claude: 15 prompts
- Midjourney: 12 prompts
- DALL-E: 8 prompts
- GitHub Copilot: 10 prompts
- Gemini: 8 prompts
- Stable Diffusion: 7 prompts
- Suno AI: 5 prompts
- Altri (distribuiti): 10 prompts

### Per Tipo:
- Text Generation: 40 prompts
- Image Generation: 25 prompts
- Code Generation: 20 prompts
- Audio/Video: 10 prompts
- Altri: 5 prompts

### Per Difficoltà:
- Beginner: 40 prompts
- Intermediate: 40 prompts
- Advanced: 20 prompts

### Per Categoria:
- Content Writing: 20 prompts
- Marketing & Sales: 15 prompts
- Code & Development: 20 prompts
- Creative Writing: 10 prompts
- Business & Productivity: 10 prompts
- Design & Art: 15 prompts
- Altri (vari): 10 prompts

## ESEMPI DI PROMPT DI QUALITÀ

### Esempio 1: ChatGPT - Content Writing
```json
{
  "title": "Professional Email Writer for Customer Support",
  "prompt_text": "You are a professional customer support representative with 10+ years of experience. Write a polite, empathetic, and solution-focused email response to a customer complaint about [ISSUE]. The email should:\n- Acknowledge the customer's frustration\n- Apologize for the inconvenience\n- Explain what happened in simple terms\n- Offer a concrete solution or next steps\n- End with a positive, helpful tone\n\nTone: Professional yet warm\nLength: 150-200 words\nCustomer Issue: [DESCRIBE ISSUE]",
  "description": "Perfect for customer support teams who need to respond to complaints professionally. Customize [ISSUE] with the specific problem. Works great for refunds, delivery issues, product defects, or service complaints. The prompt ensures empathetic, solution-focused responses that maintain brand reputation.",
  "platform": "ChatGPT",
  "prompt_type": "Text Generation",
  "category": "Customer Support",
  "tags": ["email", "customer-support", "professional", "complaint-handling", "business-communication"],
  "difficulty": "beginner",
  "estimated_tokens": 200,
  "tips": [
    "Replace [ISSUE] with the specific customer complaint",
    "Adjust tone to match your brand voice (formal, casual, friendly)",
    "Add company-specific policies or procedures if needed"
  ],
  "variables": {
    "issue": "The specific customer complaint or problem"
  }
}
```

### Esempio 2: Midjourney - Design
```json
{
  "title": "Modern Logo Design for Tech Startups",
  "prompt_text": "minimalist tech startup logo, [COMPANY NAME], [INDUSTRY], geometric shapes, modern sans-serif typography, gradient colors [COLOR1] and [COLOR2], clean lines, negative space design, vector style, simple and memorable, professional, scalable, white background --ar 1:1 --v 6",
  "description": "Creates sleek, modern logos perfect for technology companies, SaaS products, or digital startups. Adjust company name, industry focus, and color scheme to match brand identity. The geometric and minimalist approach ensures the logo works across all sizes and mediums.",
  "platform": "Midjourney",
  "prompt_type": "Image Generation",
  "category": "Design & Art",
  "tags": ["logo", "branding", "tech", "startup", "minimalist", "design"],
  "difficulty": "intermediate",
  "estimated_tokens": 80,
  "example_output": "A clean, geometric logo with gradient blue-to-purple colors, featuring stylized letters in a modern sans-serif font with clever negative space usage",
  "tips": [
    "Use --ar 1:1 for square logos, --ar 16:9 for horizontal layouts",
    "Try complementary color gradients (blue/purple, orange/red, green/teal)",
    "Add 'black and white version' for versatility",
    "Experiment with different --v versions (5.2, 6) for style variations"
  ],
  "variables": {
    "company_name": "Your company or product name",
    "industry": "SaaS, AI, fintech, e-commerce, etc.",
    "color1": "Primary brand color",
    "color2": "Secondary brand color"
  }
}
```

### Esempio 3: GitHub Copilot - Code
```json
{
  "title": "React Component Generator with TypeScript",
  "prompt_text": "// Create a React functional component in TypeScript\n// Component name: [COMPONENT_NAME]\n// Props: [LIST_PROPS]\n// Features:\n// - Uses React hooks (useState, useEffect)\n// - Properly typed with TypeScript interfaces\n// - Includes error handling\n// - Responsive design with Tailwind CSS\n// - Accessible (ARIA labels)\n// - Comprehensive JSDoc comments\n\ninterface [COMPONENT_NAME]Props {\n  // Define props here\n}\n\nconst [COMPONENT_NAME]: React.FC<[COMPONENT_NAME]Props> = (props) => {",
  "description": "Generates production-ready React components with TypeScript, following best practices. Perfect for rapidly scaffolding new components with proper typing, accessibility, and modern React patterns. Copilot will auto-complete the component logic, state management, and return JSX.",
  "platform": "GitHub Copilot",
  "prompt_type": "Code Generation",
  "category": "Code & Development",
  "tags": ["react", "typescript", "component", "frontend", "web-development"],
  "difficulty": "intermediate",
  "estimated_tokens": 150,
  "tips": [
    "Start typing in VS Code with Copilot enabled",
    "Be specific with prop names and types",
    "Copilot will suggest hooks and logic based on component name",
    "Review and customize the generated code for your specific needs"
  ],
  "variables": {
    "component_name": "Name of the component (PascalCase)",
    "list_props": "Props the component should accept"
  }
}
```

## OUTPUT FINALE

Genera il JSON completo con:
- ✅ 100 prompts totali
- ✅ Distribuzione bilanciata per piattaforma, tipo, categoria
- ✅ Alta qualità: ogni prompt testato mentalmente per utilità
- ✅ SEO-friendly: titoli e tags ottimizzati
- ✅ Varietà: mix di use cases (business, creative, technical)
- ✅ Professionalità: prompt pronti all'uso in contesti reali

## VALIDAZIONE FINALE

Prima di generare, assicurati:
- [ ] 100 prompts esatti
- [ ] JSON valido (usa validator online)
- [ ] Nessun prompt duplicato o troppo simile
- [ ] Ogni prompt ha tutti i campi richiesti
- [ ] Titoli unici e descrittivi
- [ ] Tags pertinenti e variegati
- [ ] Difficoltà distribuite correttamente
- [ ] Prompt testabili e utilizzabili immediatamente

---

**GENERA ORA IL JSON COMPLETO CON 100 PROMPTS DI ALTA QUALITÀ**

Dopo la generazione, fornisci anche:
1. Statistiche finali (count per categoria, piattaforma, difficoltà)
2. Top 5 prompts più innovativi
3. Suggerimenti per ulteriori espansioni del database
