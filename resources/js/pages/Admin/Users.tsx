"use client"

import { useState } from "react"
import { router } from '@inertiajs/react'
import AppLayout from "@/components/layouts/app-layout"
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from "@/components/ui/card"
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table"
import { Button } from "@/components/ui/button"
import { Badge } from "@/components/ui/badge"
import { Edit, Trash } from "lucide-react"
import { format } from "date-fns"
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog"
import { toast } from "sonner"
import axios from "axios"

import { TablePagination } from "@/components/ui/table-pagination"

// Интерфейсы
interface User {
    id: number
    email: string
    name: string
    role: string
    two_factor: boolean
    last_login: string | null
}

interface UsersData {
    currentPage: number
    items: User[]
    lastPage: number
    perPage: number
    total: number
}

interface Country {
    id: number
    name: string
}

interface Role {
    id: number
    name: string
}

interface Props {
    users: UsersData
    countries: Country[]
    roles: Role[]
}

interface DeleteUserResponse {
    success: boolean
    message?: string
}

// Компонент страницы
export default function Page({ users, countries, roles }: Props) {
    const [userToDelete, setUserToDelete] = useState<number | null>(null)
    const [isDeleting, setIsDeleting] = useState(false)

    // Функция для форматирования даты
    const formatDate = (dateString: string | null) => {
        if (!dateString) return "Never"
        try {
            return format(new Date(dateString), "yyyy-MM-dd")
        } catch (error) {
            return dateString
        }
    }

    // Обработчик перехода на страницу
    const handlePageChange = (page: number) => {
        router.get(
            window.location.pathname,
            { page },
            { preserveState: true, preserveScroll: true }
        )
    }

    // Обработчик редактирования пользователя
    const handleEditUser = (userId: number) => {
        router.get(`/users/${userId}/edit`)
    }

    // Обработчик удаления пользователя
    const handleDeleteUser = async () => {
        if (!userToDelete) return

        setIsDeleting(true)
        try {
            const response = await axios.delete<DeleteUserResponse>(`/api/users/${userToDelete}`)

            if (response.data.success) {
                toast.success("User deleted successfully")
            } else {
                toast.error(response.data.message || "Failed to delete user")
            }
        } catch (error) {
            toast.error("An error occurred while deleting user")
            console.error("Delete user error:", error)
        } finally {
            setIsDeleting(false)
            setUserToDelete(null)
        }
    }

    return (
        <AppLayout>
            <div className="flex items-center justify-between">
                <Button onClick={() => router.get('/users/create')}>Add User</Button>
            </div>
            <Card>
                <CardHeader className="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle>Users</CardTitle>
                        <CardDescription>User List</CardDescription>
                    </div>
                </CardHeader>
                <CardContent className="px-0 border-y">
                    <Table>
                        <TableHeader className="bg-muted/50">
                            <TableRow>
                                <TableHead className="pl-4">ID</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Name</TableHead>
                                <TableHead>Role</TableHead>
                                <TableHead>2FA</TableHead>
                                <TableHead>Last Login</TableHead>
                                <TableHead className="pr-4 text-right">Actions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {users.items.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={7} className="text-center">
                                        No users found
                                    </TableCell>
                                </TableRow>
                            ) : (
                                users.items.map((user) => (
                                    <TableRow key={user.id}>
                                        <TableCell className="pl-4">{user.id}</TableCell>
                                        <TableCell>{user.email}</TableCell>
                                        <TableCell>{user.name}</TableCell>
                                        <TableCell>
                                            <Badge variant="secondary">
                                                {user.role}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            {user.two_factor ? (
                                                <Badge variant="default">Enabled</Badge>
                                            ) : (
                                                <Badge variant="outline">Disabled</Badge>
                                            )}
                                        </TableCell>
                                        <TableCell>{user.last_login === null ? <span className="text-muted-foreground">Never</span> : formatDate(user.last_login)}</TableCell>
                                        <TableCell className="pr-4 text-right">
                                            <div className="flex justify-end gap-2">
                                                <Button
                                                    size="icon"
                                                    variant="outline"
                                                    onClick={() => handleEditUser(user.id)}
                                                >
                                                    <Edit className="h-4 w-4" />
                                                </Button>
                                                <AlertDialog>
                                                    <AlertDialogTrigger asChild>
                                                        <Button
                                                            size="icon"
                                                            variant="destructive"
                                                            onClick={() => setUserToDelete(user.id)}
                                                        >
                                                            <Trash className="h-4 w-4" />
                                                        </Button>
                                                    </AlertDialogTrigger>
                                                    <AlertDialogContent>
                                                        <AlertDialogHeader>
                                                            <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                                                            <AlertDialogDescription>
                                                                This action cannot be undone. This will permanently delete the
                                                                user and all associated data.
                                                            </AlertDialogDescription>
                                                        </AlertDialogHeader>
                                                        <AlertDialogFooter>
                                                            <AlertDialogCancel>Cancel</AlertDialogCancel>
                                                            <AlertDialogAction

                                                                className="bg-destructive hover:bg-destructive/90 text-foreground"
                                                                onClick={handleDeleteUser}
                                                                disabled={isDeleting}
                                                            >
                                                                {isDeleting ? "Deleting..." : "Delete"}
                                                            </AlertDialogAction>
                                                        </AlertDialogFooter>
                                                    </AlertDialogContent>
                                                </AlertDialog>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </CardContent>
                <CardFooter className="flex justify-center md:justify-end">
                    <TablePagination
                        currentPage={users.currentPage}
                        totalPages={users.lastPage}
                        onPageChange={handlePageChange}
                        paginationItemsToDisplay={3}
                    />
                </CardFooter>
            </Card>
        </AppLayout>
    )
}
