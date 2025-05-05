import { useState, useEffect, useCallback } from 'react';

interface WebSocketMessage {
    type: string;
    [key: string]: any;
}

export const useWebSocket = (channel: string) => {
    const [socket, setSocket] = useState<WebSocket | null>(null);
    const [lastMessage, setLastMessage] = useState<MessageEvent | null>(null);

    useEffect(() => {
        const ws = new WebSocket(`ws://${window.location.hostname}:8080/app/ws`);
        
        ws.onopen = () => {
            console.log('WebSocket Connected');
            ws.send(JSON.stringify({
                type: 'subscribe',
                channel: channel
            }));
        };

        ws.onmessage = (event) => {
            setLastMessage(event);
        };

        ws.onerror = (error) => {
            console.error('WebSocket Error:', error);
        };

        ws.onclose = () => {
            console.log('WebSocket Disconnected');
        };

        setSocket(ws);

        return () => {
            ws.close();
        };
    }, [channel]);

    const sendMessage = useCallback((message: WebSocketMessage) => {
        if (socket && socket.readyState === WebSocket.OPEN) {
            socket.send(JSON.stringify({
                ...message,
                channel: channel
            }));
        }
    }, [socket, channel]);

    return {
        sendMessage,
        lastMessage
    };
}; 