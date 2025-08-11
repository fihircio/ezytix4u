<template>
  <div class="modal modal-mask" style="display:block;">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" style="background: linear-gradient(90deg, #2dd4bf, #60a5fa); color: #fff;">
          <h5 class="modal-title d-flex align-items-center">
            <i class="fas fa-magic me-2"></i>
            {{ modalTitle }}
          </h5>
          <button type="button" class="btn btn-sm bg-light text-dark" @click="$emit('close')">✕</button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <h6 class="mb-2">Describe your event</h6>
            <textarea v-model="prompt" class="form-control" rows="6" :placeholder="placeholder"></textarea>
            <div class="text-muted small mt-1">{{ charCount }} characters</div>
          </div>

          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" type="button" @click="clearPrompt">
              <i class="fas fa-eraser"></i> Clear
            </button>
            <button class="btn btn-success ms-auto" type="button" :disabled="!canWrite" @click="generate">
              <i class="fas fa-magic"></i> Write
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AiModal',
  props: {
    seoOnly: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      prompt: ''
    };
  },
  computed: {
    placeholder() {
      if (this.seoOnly) {
        return 'Describe your event for SEO optimization... For example: A rock concert featuring popular bands happening in New York City on 21st June 2026, targeting music lovers and concert-goers.';
      }
      return 'Tell me about your event... For example: A rock concert featuring popular bands happening in New York City on 21st June 2026, targeting music lovers and concert-goers.';
    },
    modalTitle() {
      return this.seoOnly ? 'Generate SEO with AI' : 'Update with AI';
    },
    charCount() {
      return this.prompt?.length || 0;
    },
    canWrite() {
      return (this.prompt || '').trim().length > 5;
    }
  },
  methods: {
    clearPrompt() {
      this.prompt = '';
    },
    async generate() {
      const text = (this.prompt || '').trim();
      try {
        console.log('AI Modal: Calling API with prompt:', text);
        const res = await axios.post(route('eventmie.ai_generate'), { prompt: text });
        console.log('AI Modal: API response:', res.data);
        
        if (res.data && res.data.status && res.data.data) {
          const d = res.data.data;
          console.log('AI Modal: Emitting data:', d);
          
          if (this.seoOnly) {
            // SEO-only mode: only emit SEO data
            this.$emit('apply', {
              meta_title: d.meta_title,
              meta_description: d.meta_description,
              meta_keywords: d.meta_keywords,
            });
          } else {
            // Full mode: emit content data only (no SEO)
            this.$emit('apply', {
              title: d.title,
              excerpt: d.excerpt,
              description: d.description,
              faq: d.faq,
              // No SEO fields for Details tab
            });
          }
          
          this.$emit('close');
          return;
        }
      } catch (e) {
        console.log('AI Modal: API call failed, using fallback:', e);
        // fallback to local generation if API not configured or fails
        const title = this.generateTitle(text);
        const excerpt = this.generateExcerpt(text);
        const description = this.generateDescription(text);
        const faq = this.generateFaq(text);
        const meta = this.generateSeo(title, excerpt);
        console.log('AI Modal: Fallback data:', { title, excerpt, description, faq, meta });
        
        if (this.seoOnly) {
          // SEO-only mode: only emit SEO data
          this.$emit('apply', {
            meta_title: meta.meta_title,
            meta_description: meta.meta_description,
            meta_keywords: meta.meta_keywords
          });
        } else {
          // Full mode: emit content data only (no SEO)
          this.$emit('apply', {
            title,
            excerpt,
            description,
            faq,
            // No SEO fields for Details tab
          });
        }
        
        this.$emit('close');
      }
    },
    sentenceize(str) {
      const s = str.replace(/\s+/g, ' ').trim();
      if (!s) return '';
      return s.charAt(0).toUpperCase() + s.slice(1).replace(/([^.?!])$/, '$1.');
    },
    generateTitle(text) {
      const first = text.split(/[.!?]/)[0].trim();
      const candidate = first.length > 8 ? first : (text.slice(0, 60));
      return this.titleCase(candidate).slice(0, 80);
    },
    titleCase(s) {
      return s.replace(/\w\S*/g, w => w.charAt(0).toUpperCase() + w.substr(1).toLowerCase());
    },
    generateExcerpt(text) {
      const s = this.sentenceize(text);
      return s.slice(0, 160);
    },
    generateDescription(text) {
      const s = this.sentenceize(text);
      const details = `\n\nWhat to expect:\n- Engaging sessions and memorable moments\n- Great ambiance and seamless experience\n- Suitable for all audiences`;
      return `<p>${s}</p><p>${details.replace(/\n/g, '<br>')}</p>`;
    },
    generateFaq(text) {
      const s = this.sentenceize(text);
      return `<p><strong>FAQs</strong></p>
<ul>
  <li><strong>Who should attend?</strong> ${s || 'Anyone interested in the topic.'}</li>
  <li><strong>Where is it?</strong> Venue details will be provided after registration.</li>
  <li><strong>What should I bring?</strong> Valid ID and your ticket (digital or printed).</li>
  <li><strong>Is there a refund policy?</strong> Refer to the event’s refund policy on the checkout page.</li>
  <li><strong>How do I contact the organiser?</strong> Use the contact form on the event page.</li>
</ul>`;
    },
    generateSeo(title, excerpt) {
      const meta_title = title.slice(0, 60);
      const meta_description = excerpt.slice(0, 155);
      const meta_keywords = this.extractKeywords(`${title} ${excerpt}`);
      return { meta_title, meta_description, meta_keywords };
    },
    extractKeywords(text) {
      const words = (text.toLowerCase().match(/[a-z0-9]+/g) || [])
        .filter(w => w.length > 3);
      const freq = {};
      words.forEach(w => { freq[w] = (freq[w] || 0) + 1; });
      return Object.keys(freq).sort((a,b)=>freq[b]-freq[a]).slice(0, 8).join(', ');
    }
  }
};
</script>

<style scoped>
.modal-mask {
  position: fixed;
  z-index: 1050;
  top: 0; left: 0; right: 0; bottom: 0;
  background-color: rgba(0,0,0,0.5);
}
.gap-2 { gap: .5rem; }
</style>


