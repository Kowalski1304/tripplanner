"use client"

import { Head, Link, usePage } from "@inertiajs/react"
import { useState } from "react"
import axios from 'axios';

interface User {
    id: number
    name: string
    profile: {
        id: number
        bio?: string
        location?: string
        files?: {
            id: number
            path: string
        }[]
    }
}

interface Props {
    user: User
    isContact: boolean
    csrf?: string
}

export default function PublicProfile({ user, isContact = false, csrf }: Props) {
    const [isInContacts, setIsInContacts] = useState(isContact)
    const [loading, setLoading] = useState(false)

    const { props } = usePage()

    const handleAddContact = async () => {
        if (loading) return

        setLoading(true)

        try {
            const token = localStorage.getItem('token')

            const response = await axios.post(`/api/contacts/add/${user.id}`, {}, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': token ? `Bearer ${token}` : '',
                    'X-CSRF-TOKEN': csrf || (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content,
                },
                withCredentials: true
            });

            if (response.status === 200 || response.status === 201) {
                setIsInContacts(true)
                return true;
            }
        } catch (error) {
            console.error('Error adding contact:', error);
            // Тут можна додати обробку помилок авторизації
            // Наприклад, перенаправлення на сторінку входу, якщо статус 401
            if (axios.isAxiosError(error) && error.response?.status === 401) {
                // Перенаправлення на сторінку входу
                window.location.href = '/login';
            }
            return false;
        } finally {
            setLoading(false)
        }
    };

    return (
        <div className="min-h-screen bg-gray-100 dark:bg-gray-900">
            <Head title={`Профіль ${user.name}`} />

            <div className="py-12">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        {/* Верхня частина профілю з фото */}
                        <div className="relative h-48 bg-gradient-to-r from-blue-500 to-indigo-600">
                            <div className="absolute -bottom-16 left-6">
                                <div className="h-32 w-32 overflow-hidden rounded-full border-4 border-white bg-white dark:border-gray-800">
                                    {user.profile?.files && user.profile.files.length > 0 ? (
                                        <img
                                            src={user.profile.files[0].path || "/placeholder.svg"}
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
                                    {user.profile?.location && (
                                        <p className="mt-1 text-gray-600 dark:text-gray-400">{user.profile.location}</p>
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
                                            disabled
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

                                    <Link
                                        href={route("messages.create", { userId: user.id })}
                                        className="inline-flex items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            className="mr-2 h-5 w-5"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z" />
                                            <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z" />
                                        </svg>
                                        Написати
                                    </Link>
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
        </div>
    )
}
