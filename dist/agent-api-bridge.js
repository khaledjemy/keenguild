(() => {
  const readMeta = name => document.querySelector(`meta[name="${name}"]`)?.content.trim() || '';
  const apiBase = readMeta('keenguild-agent-api').replace(/\/+$/, '');
  const siteKey = readMeta('keenguild-agent-site-key');
  let conversationId = globalThis.crypto?.randomUUID?.() || `${Date.now()}-${Math.random().toString(36).slice(2)}`;

  if (typeof serviceChatCopy !== 'undefined') {
    Object.assign(serviceChatCopy.ar, {
      status: apiBase && siteKey ? 'مساعد KeenGuild · متصل بالخدمة' : 'مساعد KeenGuild · غير مربوط',
      note: 'تُرسل الرسائل إلى خدمة المساعد المستقلة ويُحفظ سجل المحادثة.',
      error: 'تعذّر الوصول إلى خدمة المساعد. تحقق من اتصال الخدمة وحاول مرة أخرى.',
    });
    Object.assign(serviceChatCopy.en, {
      status: apiBase && siteKey ? 'KeenGuild assistant · API connected' : 'KeenGuild assistant · not connected',
      note: 'Messages are sent to the standalone assistant service and the conversation is saved.',
      error: 'Could not reach the assistant service. Check the connection and try again.',
    });
    if (typeof syncServiceChatLanguage === 'function') syncServiceChatLanguage();
  }

  const messages = {
    ar: {
      notConfigured: 'المساعد غير مربوط بعد. أضف عنوان خدمة الـAPI ومفتاح هذا الموقع إلى إعدادات النشر.',
      unavailable: 'تعذّر الوصول إلى المساعد الآن. حاول مرة أخرى بعد قليل.',
    },
    en: {
      notConfigured: 'The assistant is not connected yet. Add the API service URL and this site’s key to the deployment config.',
      unavailable: 'The assistant is temporarily unavailable. Please try again shortly.',
    },
  };

  window.KeenGuildAgent = {
    async request(message, onText) {
      if (!apiBase || !siteKey) throw new Error(messages[document.documentElement.lang === 'en' ? 'en' : 'ar'].notConfigured);

      const response = await fetch(`${apiBase}/api/public/${encodeURIComponent(siteKey)}/chat`, {
        method: 'POST',
        mode: 'cors',
        credentials: 'omit',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message, conversation_id: conversationId }),
      });

      if (!response.ok) {
        if (response.status === 403) {
          throw new Error(document.documentElement.lang === 'en'
            ? 'This website origin is not authorized for the assistant.'
            : 'عنوان هذا الموقع غير مضاف ضمن المواقع المصرح لها بالمساعد.');
        }
        if (response.status === 404) {
          throw new Error(document.documentElement.lang === 'en'
            ? 'The assistant workspace key was not found.'
            : 'مفتاح مساحة المساعد لهذا الموقع غير صحيح.');
        }
        let reason = '';
        try { reason = (await response.json()).detail || ''; } catch {}
        throw new Error(reason || messages[document.documentElement.lang === 'en' ? 'en' : 'ar'].unavailable);
      }
      if (!response.body) throw new Error(messages[document.documentElement.lang === 'en' ? 'en' : 'ar'].unavailable);

      const reader = response.body.getReader();
      const decoder = new TextDecoder();
      let buffer = '';
      let answer = '';
      let streamError = '';

      const consumePacket = packet => {
        const event = packet.match(/^event:\s*(.+)$/m)?.[1]?.trim();
        const dataText = packet.split(/\r?\n/)
          .filter(line => line.startsWith('data:'))
          .map(line => line.slice(5).trimStart())
          .join('\n');
        if (!dataText) return;
        const data = JSON.parse(dataText);
        if (event === 'meta' && data.conversation_id) conversationId = data.conversation_id;
        if (event === 'token' && data.text !== undefined) {
          const piece = String(data.text);
          answer += piece;
          onText(piece);
        }
        if (event === 'error') streamError = data.message || messages[document.documentElement.lang === 'en' ? 'en' : 'ar'].unavailable;
      };

      while (true) {
        const { value, done } = await reader.read();
        buffer += decoder.decode(value || new Uint8Array(), { stream: !done });
        let boundary;
        while ((boundary = buffer.search(/\r?\n\r?\n/)) !== -1) {
          const packet = buffer.slice(0, boundary);
          const separator = buffer.slice(boundary).match(/^\r?\n\r?\n/)[0];
          buffer = buffer.slice(boundary + separator.length);
          consumePacket(packet);
        }
        if (done) break;
      }
      if (buffer.trim()) consumePacket(buffer);
      if (streamError) throw new Error(messages[document.documentElement.lang === 'en' ? 'en' : 'ar'].unavailable);
      if (!answer.trim()) throw new Error(messages[document.documentElement.lang === 'en' ? 'en' : 'ar'].unavailable);
      return answer.trim();
    },
  };
})();
