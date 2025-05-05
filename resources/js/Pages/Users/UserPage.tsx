"use client"

import { Link } from "@inertiajs/react"
import { useState, useEffect } from "react"
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useWebSocket } from '@/Hooks/useWebSocket';

interface User {
    id: number
    name: string
    profile: {
        id: number
        bio?: string
        address?: string
    }
    files?: {
        id: number
        path: string
    }[]
}

interface Props {
    user: User
    isContact: boolean
    broadcastChannel: string
}

export default function PublicProfile({ user, isContact = false, broadcastChannel }: Props) {
    const [isInContacts, setIsInContacts] = useState(isContact)
    const [loading, setLoading] = useState(false)
    const { sendMessage, lastMessage } = useWebSocket(broadcastChannel);

    useEffect(() => {
        if (lastMessage) {
            const data = JSON.parse(lastMessage.data);
            if (data.type === 'contact_status') {
                setIsInContacts(data.isContact);
            }
        }
    }, [lastMessage]);

    const handleAddContact = async () => {
        try {
            setLoading(true);
            sendMessage({
                type: 'add_contact',
                userId: user.id
            });
        } catch (error) {
            console.error('Error adding contact:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleDeleteContact = async () => {
        try {
            setLoading(true);
            sendMessage({
                type: 'remove_contact',
                userId: user.id
            });
        } catch (error) {
            console.error('Error removing contact:', error);
        } finally {
            setLoading(false);
        }
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Profile</h2>}
        >
            <div className="py-12">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        {/* Верхня частина профілю з фото */}
                        <div className="relative h-48 bg-gradient-to-r from-blue-500 to-indigo-600">
                            <div className="absolute -bottom-16 left-6">
                                <div className="h-32 w-32 overflow-hidden rounded-full border-4 border-white bg-white dark:border-gray-800">
                                    {user?.files && user.files.length > 0 ? (
                                        <img
                                            src={user.files[0].path || "/placeholder.svg"}
                                            alt={`${user.name}`}
                                            className="h-full w-full object-cover"
                                        />
                                    ) : (
                                        <div className="flex h-full w-full items-center justify-center bg-gray-200 text-2xl font-bold text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                            {user.name.charAt(0).toUpperCase()}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Інформація про користувача */}
                        <div className="mt-16 p-6 text-gray-900 dark:text-gray-100">
                            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h1 className="text-2xl font-bold">{user.name}</h1>
                                    {user.profile?.address && (
                                        <p className="mt-1 text-gray-600 dark:text-gray-400">{user.profile.address}</p>
                                    )}
                                </div>

                                <div className="mt-4 flex space-x-3 sm:mt-0">
                                    {!isInContacts ? (
                                        <button
                                            onClick={handleAddContact}
                                            disabled={loading}
                                            className="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-70"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                className="mr-2 h-5 w-5"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                                            </svg>
                                            {loading ? 'Додаємо...' : 'Додати в контакти'}
                                        </button>
                                    ) : (
                                        <button
                                            onClick={handleDeleteContact}
                                            disabled={loading}
                                            className="inline-flex items-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                className="mr-2 h-5 w-5"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fillRule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clipRule="evenodd"
                                                />
                                            </svg>
                                            В контактах
                                        </button>
                                    )}
                                </div>
                            </div>

                            {/* Біографія користувача */}
                            {user.profile?.bio && (
                                <div className="mt-6">
                                    <h2 className="text-lg font-medium">Про мене</h2>
                                    <p className="mt-2 whitespace-pre-line text-gray-700 dark:text-gray-300">{user.profile.bio}</p>
                                </div>
                            )}

                            {/* Тут можна додати додаткові секції профілю */}
                            <div className="mt-8 border-t border-gray-200 pt-6 dark:border-gray-700">
                                <h2 className="text-lg font-medium">Активність</h2>
                                <p className="mt-2 text-gray-600 dark:text-gray-400">
                                    Інформація про активність користувача буде відображатися тут.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    )
}
