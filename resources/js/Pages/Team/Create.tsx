"use client"

import { useState, useRef, type FormEvent, type ChangeEvent } from "react"
import { Link } from "@inertiajs/react"
import route from "ziggy-js"
import axios from "axios"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout"

interface User {
    id: number
    name: string
    profile?: {
        files?: {
            id: number
            path: string
        }[]
    }
}

interface Props {
    contacts: User[]
}

export default function TeamCreate({ contacts }: Props) {
    const [teamName, setTeamName] = useState("")
    const [description, setDescription] = useState("")
    const [selectedContacts, setSelectedContacts] = useState<number[]>([])
    const [teamPhoto, setTeamPhoto] = useState<File | null>(null)
    const [photoPreview, setPhotoPreview] = useState<string | null>(null)
    const [isSubmitting, setIsSubmitting] = useState(false)
    const [errors, setErrors] = useState<{
        name?: string
        description?: string
        photo?: string
        contacts?: string
    }>({})

    const fileInputRef = useRef<HTMLInputElement>(null)

    const handlePhotoClick = () => {
        fileInputRef.current?.click()
    }

    const handlePhotoChange = (e: ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0]
            setTeamPhoto(file)
            setPhotoPreview(URL.createObjectURL(file))
        }
    }

    const toggleContact = (contactId: number) => {
        setSelectedContacts((prev) => {
            if (prev.includes(contactId)) {
                return prev.filter((id) => id !== contactId)
            } else {
                return [...prev, contactId]
            }
        })
    }

    const validateForm = (): boolean => {
        const newErrors: {
            name?: string
            description?: string
            photo?: string
            contacts?: string
        } = {}

        if (!teamName.trim()) {
            newErrors.name = "Назва команди обов'язкова"
        }

        if (selectedContacts.length === 0) {
            newErrors.contacts = "Виберіть хоча б одного учасника"
        }

        setErrors(newErrors)
        return Object.keys(newErrors).length === 0
    }

    const handleSubmit = async (e: FormEvent) => {
        const token = localStorage.getItem('api_token');
        e.preventDefault()

        if (!validateForm()) {
            return
        }

        setIsSubmitting(true)

        const formData = new FormData()
        formData.append("name", teamName)
        formData.append("description", description)
        selectedContacts.forEach((contactId) => {
            formData.append("contacts[]", contactId.toString())
        })

        if (teamPhoto) {
            formData.append("photo", teamPhoto)
        }

        try {
            const response = await axios.post("/api/team/create", formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                    'Authorization': `Bearer ${token}`
                },
                withCredentials: true
            })

            if (response.status === 200 || response.status === 201) {
                window.location.href = "{{ route('team.show', ':id') }}".replace(':id', response.data.id);
            }
            return false;
        } catch (error: any) {
            if (error.response && error.response.data && error.response.data.errors) {
                setErrors(error.response.data.errors)
            } else {
                setErrors({
                    name: "Сталася помилка при створенні команди. Спробуйте ще раз.",
                })
            }
            setIsSubmitting(false)
        }
    }

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Profile</h2>}
        >
            <div className="py-12">
                <div className="max-w-3xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="flex items-center mb-6">
                                <Link
                                    // href={route("teams")}
                                    className="mr-4 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        className="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18"
                                        />
                                    </svg>
                                </Link>
                                <h2 className="text-xl font-semibold text-gray-800 dark:text-gray-200">Створення нової команди</h2>
                            </div>

                            <form onSubmit={handleSubmit}>
                                {/* Фото команди */}
                                <div className="mb-6 flex flex-col items-center">
                                    <div
                                        onClick={handlePhotoClick}
                                        className="w-32 h-32 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 cursor-pointer flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-400 transition-colors"
                                    >
                                        {photoPreview ? (
                                            <img
                                                src={photoPreview || "/placeholder.svg"}
                                                alt="Фото команди"
                                                className="w-full h-full object-cover"
                                            />
                                        ) : (
                                            <div className="text-center p-4">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    className="h-10 w-10 mx-auto text-gray-400 dark:text-gray-500"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        strokeWidth={2}
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                    />
                                                </svg>
                                                <p className="mt-1 text-xs text-gray-500 dark:text-gray-400">Додати фото</p>
                                            </div>
                                        )}
                                    </div>
                                    <input
                                        type="file"
                                        ref={fileInputRef}
                                        onChange={handlePhotoChange}
                                        className="hidden"
                                        accept="image/*"
                                    />
                                    {errors.photo && <p className="mt-2 text-sm text-red-600 dark:text-red-400">{errors.photo}</p>}
                                    <p className="mt-2 text-sm text-gray-500 dark:text-gray-400">Натисніть, щоб додати фото команди</p>
                                </div>

                                {/* Назва команди */}
                                <div className="mb-4">
                                    <label htmlFor="teamName" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Назва команди *
                                    </label>
                                    <input
                                        type="text"
                                        id="teamName"
                                        value={teamName}
                                        onChange={(e) => setTeamName(e.target.value)}
                                        className={`w-full px-3 py-2 border ${errors.name ? "border-red-500 dark:border-red-500" : "border-gray-300 dark:border-gray-600"} rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white`}
                                        placeholder="Введіть назву команди"
                                    />
                                    {errors.name && <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.name}</p>}
                                </div>

                                {/* Опис команди */}
                                <div className="mb-6">
                                    <label
                                        htmlFor="description"
                                        className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
                                    >
                                        Опис
                                    </label>
                                    <textarea
                                        id="description"
                                        value={description}
                                        onChange={(e) => setDescription(e.target.value)}
                                        rows={4}
                                        className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="Опишіть вашу команду"
                                    ></textarea>
                                    {errors.description && (
                                        <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.description}</p>
                                    )}
                                </div>

                                {/* Список контактів */}
                                <div className="mb-6">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Виберіть учасників команди *
                                    </label>

                                    {errors.contacts && <p className="mb-2 text-sm text-red-600 dark:text-red-400">{errors.contacts}</p>}

                                    <div className="border border-gray-300 dark:border-gray-600 rounded-md overflow-hidden">
                                        <div className="p-2 bg-gray-50 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600">
                                            <p className="text-sm text-gray-500 dark:text-gray-400">
                                                Вибрано: {selectedContacts.length} {selectedContacts.length === 1 ? "учасник" : "учасників"}
                                            </p>
                                        </div>

                                        <div className="max-h-60 overflow-y-auto">
                                            {contacts.length > 0 ? (
                                                <ul className="divide-y divide-gray-200 dark:divide-gray-700">
                                                    {contacts.map((contact) => (
                                                        <li
                                                            key={contact.id}
                                                            className={`flex items-center justify-between p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 ${selectedContacts.includes(contact.id) ? "bg-blue-50 dark:bg-blue-900/20" : ""}`}
                                                            onClick={() => toggleContact(contact.id)}
                                                        >
                                                            <div className="flex items-center">
                                                                <div className="w-10 h-10 rounded-full overflow-hidden bg-gray-300 dark:bg-gray-600">
                                                                    {contact.profile?.files && contact.profile.files.length > 0 ? (
                                                                        <img
                                                                            src={contact.profile.files[0].path || "/placeholder.svg"}
                                                                            alt={contact.name}
                                                                            className="w-full h-full object-cover"
                                                                        />
                                                                    ) : (
                                                                        <div className="w-full h-full flex items-center justify-center text-lg font-semibold text-gray-700 dark:text-gray-300">
                                                                            {contact.name.charAt(0).toUpperCase()}
                                                                        </div>
                                                                    )}
                                                                </div>
                                                                <div className="ml-3">
                                                                    <p className="text-sm font-medium text-gray-900 dark:text-gray-100">{contact.name}</p>
                                                                </div>
                                                            </div>

                                                            <div
                                                                className={`w-6 h-6 rounded-full flex items-center justify-center ${selectedContacts.includes(contact.id) ? "bg-blue-500 text-white" : "border border-gray-300 dark:border-gray-600"}`}
                                                            >
                                                                {selectedContacts.includes(contact.id) && (
                                                                    <svg
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        className="h-4 w-4"
                                                                        fill="none"
                                                                        viewBox="0 0 24 24"
                                                                        stroke="currentColor"
                                                                    >
                                                                        <path
                                                                            strokeLinecap="round"
                                                                            strokeLinejoin="round"
                                                                            strokeWidth={2}
                                                                            d="M5 13l4 4L19 7"
                                                                        />
                                                                    </svg>
                                                                )}
                                                            </div>
                                                        </li>
                                                    ))}
                                                </ul>
                                            ) : (
                                                <div className="p-4 text-center text-gray-500 dark:text-gray-400">У вас немає контактів</div>
                                            )}
                                        </div>
                                    </div>
                                </div>

                                {/* Кнопки дій */}
                                <div className="flex justify-end space-x-3">
                                    <Link
                                        // href={route("teams")}
                                        className="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                                    >
                                        Скасувати
                                    </Link>
                                    <button
                                        type="submit"
                                        disabled={isSubmitting}
                                        className="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        {isSubmitting ? "Створення..." : "Створити команду"}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    )
}

