import React, { useState } from 'react';
import { createPortal } from 'react-dom';
import '../styles/modal.css';

interface TelegramModalProps {
  onClose: () => void;
  onSuccess: (data: { botToken: string; chatId: string }) => void;
}

export const TelegramModal: React.FC<TelegramModalProps> = ({ onClose, onSuccess }) => {
  const [botToken, setBotToken] = useState('');
  const [chatId, setChatId] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (event: React.FormEvent) => {
    event.preventDefault();
    setIsLoading(true);
    setError('');

    try {
      if (!botToken.includes(':')) {
        throw new Error('Неверный формат токена бота.');
      }
      if (!/^-?\d+$/.test(chatId)) {
        throw new Error('Chat ID должен быть числом.');
      }

      const response = await fetch(
        `https://api.telegram.org/bot${botToken}/sendMessage?chat_id=${chatId}&text=Подключение успешно!`
      );

      const data = await response.json();

      if (!data.ok) {
        throw new Error(data.description || 'Ошибка при отправке тестового сообщения.');
      }

      onSuccess({ botToken, chatId });
      onClose();
    } catch (err: any) {
      setError(err.message);
    } finally {
      setIsLoading(false);
    }
  };


  return createPortal(
    <div className="popup-container">
      <div className="popup-content" onClick={(e) => e.stopPropagation()}>
        <div className="popup-header">
          <h2>Подключение Telegram бота</h2>
          <button className="close-button" onClick={onClose} aria-label="Закрыть">
            &times;
          </button>
        </div>
        <form className="popup-form" onSubmit={handleSubmit}>
          <input
            type="text"
            placeholder="Bot Token"
            value={botToken}
            onChange={(e) => setBotToken(e.target.value)}
            required
          />
          <input
            type="text"
            placeholder="Chat ID"
            value={chatId}
            onChange={(e) => setChatId(e.target.value)}
            required
          />
          {error && <p style={{ color: 'red' }}>{error}</p>}
          <button type="submit" disabled={isLoading}>
            {isLoading ? 'Подключение...' : 'Подключить'}
          </button>
        </form>
      </div>
    </div>,
    document.body
  );
};