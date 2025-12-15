import React, { useEffect, useState } from 'react';
import './App.css';

type TelegramStatus = {
  enabled: boolean;
  bot_token: string;
  chat_id: string;
  created_at: string;
  updated_at: string;
  lastSentAt: string | null;
  sent7d: number;
  failed7d: number;
};

const formatDateTime = (value: string | null) => {
  if (!value) return '—';
  const date = new Date(value);
  // Если дата некорректна, возвращаем исходное значение
  return Number.isNaN(date.getTime()) ? value : date.toLocaleString();
};

const jsonLdHeaders = { Accept: 'application/ld+json' };
const jsonLdPostHeaders = {
  Accept: 'application/ld+json',
  'Content-Type': 'application/ld+json',
};

const App: React.FC = () => {
  const [bot_token, setBotToken] = useState('');
  const [chat_id, setChatId] = useState('');
  const [enabled, setEnabled] = useState(false);
  const [status, setStatus] = useState<TelegramStatus | null>(null);
  const [statusError, setStatusError] = useState('');
  const [formError, setFormError] = useState('');
  const [saving, setSaving] = useState(false);
  const [savedMessage, setSavedMessage] = useState('');
  const [loadingStatus, setLoadingStatus] = useState(false);


  const loadStatus = async () => {
    setLoadingStatus(true);
    setStatusError('');
    const shopId = getShopId();
    try {
      const response = await fetch(`/api/shops/${shopId}/telegram/status`, { headers: jsonLdHeaders });
      if (!response.ok && response.status !== 404) {
        throw new Error('Не удалось получить статус');
      }
      const data: TelegramStatus = await response.json();
      setStatus(data);
      setBotToken(data.bot_token);
      setChatId(data.chat_id);
      setEnabled(Boolean(data.enabled));
    } catch (err: unknown) {
      console.error(err);
      const message = err instanceof Error ? err.message : 'Ошибка при получении статуса';
      setStatusError(message);
    } finally {
      setLoadingStatus(false);
    }
  };


  const getShopId = () => {
    // берем id магазина из url
    const path = window.location.pathname;
    const shopId = path.split('/')[2];
    const regex = /^\d+(\.\d+)?$/;
    // если id не число, то берем 1
    if (!regex.test(shopId)) {
      return 2; 
    }
    return parseInt(shopId);
  }

  useEffect(() => {
    loadStatus();
  }, []);

  const handleSubmit = async (event: React.FormEvent) => {
    event.preventDefault();
    setFormError('');
    setSavedMessage('');

    if (!bot_token || !chat_id) {
      setFormError('Заполните токен и chatId');
      return;
    }

    setSaving(true);
    const shopId = getShopId(); 
    try {
      const response = await fetch(`/api/shops/${shopId}/telegram/connect`, {
        method: 'POST',
        headers: jsonLdPostHeaders,
        body: JSON.stringify({ bot_token, chat_id, enabled }),
      });

      if (!response.ok) {
        const text = await response.text();
        throw new Error(text || 'Не удалось сохранить настройки');
      }

      setSavedMessage('Настройки сохранены');
      await loadStatus();
    } catch (err: unknown) {
      const message = err instanceof Error ? err.message : 'Не удалось сохранить настройки';
      setFormError(message);
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="page">
      <div className="card">
        <h1>Настройки Telegram</h1>
        <p className="subtitle">Укажите данные бота и статус отправки уведомлений</p>

        <form className="form" onSubmit={handleSubmit}>
          <label className="field">
            <span>Bot Token</span>
            <input
              type="text"
              placeholder="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11"
              value={bot_token}
              onChange={(e) => setBotToken(e.target.value)}
              autoComplete="off"
            />
          </label>

          <label className="field">
            <span>Chat ID</span>
            <input
              type="text"
              placeholder="Например: 123456789"
              value={chat_id}
              onChange={(e) => setChatId(e.target.value)}
              autoComplete="off"
            />
          </label>

          <label className="toggle">
            <input
              type="checkbox"
              checked={enabled}
              onChange={(e) => setEnabled(e.target.checked)}
            />
            <span>Включить отправку уведомлений</span>
          </label>

          {formError && <div className="error">{formError}</div>}
          {savedMessage && <div className="success">{savedMessage}</div>}

          <button type="submit" className="primary" disabled={saving}>
            {saving ? 'Сохранение...' : 'Сохранить'}
          </button>
        </form>

        <div className="hint">
          <h3>Как узнать Bot Token:</h3>
          <p>Откройте Telegram и найдите в поиске @BotFather (у него будет синяя галочка верификации)</p>
          <p>Нажмите "Start" или отправьте команду /start.</p>
          <p>Отправьте команду /newbot для создания нового бота.</p>
          <p>Введите имя бота (то, что будет отображаться в чатах).</p>
          <p>Введите юзернейм (уникальный публичный адрес, должен заканчиваться на bot, например, MySuperBot или My_super_bot)</p>
          <p>Если юзернейм свободен, BotFather пришлет сообщение с поздравлением и токеном — скопируйте его и сохраните в надежном месте</p>
        </div>

        <div className="hint">
          Как узнать chatId: напишите любое сообщение боту @userinfobot и он ответит вашим ID..
        </div>
      </div>

      <div className="card status">
        <div className="status-header">
          <h2>Статус</h2>
          {loadingStatus && <span className="badge">обновление...</span>}
        </div>
        {statusError && <div className="error">{statusError}</div>}
        {status && (
          <div className="status-grid">
            <div>
              <span className="label">Включено</span>
              <div className="value">{status.enabled ? 'Да' : 'Нет'}</div>
            </div>
            <div>
              <span className="label">Последняя отправка</span>
              <div className="value">{formatDateTime(status.lastSentAt)}</div>
            </div>
            <div>
              <span className="label">Отправлено / Ошибок (7 дней)</span>
              <div className="value">{status.sent7d} / {status.failed7d}</div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default App;