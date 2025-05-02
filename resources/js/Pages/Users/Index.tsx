import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout"
import { Head } from "@inertiajs/react"

interface User {
    id: number
    name: string
    profile: {
        id: number
        user_id: number
        files?: {
            id: number
            path: string
        }[]
    }
}

interface Props {
    users: User[]
}

export default function Index({ users }: Props) {
    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Users</h2>}
        >
            <Head title="Users" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <h3 className="mb-6 text-lg font-medium">User List</h3>

                            <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                                {users.map((user) => (
                                    <div
                                        key={user.id}
                                    >
                                        <a href={`/users/${user.id}`}
                                           className="flex items-center gap-4 rounded-lg border p-4 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700"
                                        >
                                            <div className="h-12 w-12 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                                {user.profile?.files && user.profile.files.length > 0 ? (
                                                    <img
                                                        src={user.profile.files[0].path || "/placeholder.svg"}
                                                        alt={`${user.name}'s profile`}
                                                        className="h-full w-full object-cover"
                                                    />
                                                ) : (
                                                    <div className="flex h-full w-full items-center justify-center text-gray-500 dark:text-gray-400">
                                                        {user.name.charAt(0).toUpperCase()}
                                                    </div>
                                                )}
                                            </div>
                                            <div>
                                                <h4 className="font-medium">{user.name}</h4>
                                                <p className="text-sm text-gray-500 dark:text-gray-400">User ID: {user.id}</p>
                                            </div>
                                        </a>
                                    </div>
                                ))}
                            </div>

                            {users.length === 0 && <p className="text-center text-gray-500 dark:text-gray-400">No users found.</p>}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    )
}
