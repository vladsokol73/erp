import React from "react"
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from "@/components/ui/breadcrumb"
import { usePage } from '@inertiajs/react'

/**
 * Интерфейс для сегмента хлебных крошек
 */
interface BreadcrumbSegment {
    title: string
    isActive: boolean
}

/**
 * Пропсы для компонента AppBreadcrumb
 */
interface AppBreadcrumbProps {
    className?: string
    homeTitle?: string
    excludePaths?: string[]
}

/**
 * Компонент визуальных хлебных крошек для Inertia.js,
 * которые показывают текущий путь без использования ссылок для навигации.
 */
export default function AppBreadcrumb({
                                          className,
                                          homeTitle = "Home",
                                          excludePaths = ["/"],
                                      }: AppBreadcrumbProps) {
    // Получаем текущий URL из Inertia
    const { url } = usePage()

    // Если текущий путь в списке исключений, не показываем крошки
    if (excludePaths.includes(url)) {
        return null
    }

    // Генерируем сегменты хлебных крошек
    const segments: BreadcrumbSegment[] = []

    // Добавляем главную страницу, если мы не на ней
    if (url !== "/") {
        segments.push({
            title: homeTitle,
            isActive: false,
        })
    }

    // Обрабатываем сегменты пути
    if (url) {
        // Убираем параметры URL если они есть
        const pathname = url.split('?')[0]
        const pathSegments = pathname.split('/').filter(Boolean)

        pathSegments.forEach((segment, index) => {
            const isActive = index === pathSegments.length - 1

            // Форматируем заголовок: преобразуем kebab-case в нормальный текст
            const title = segment
                .replace(/-/g, ' ')
                .split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ')

            segments.push({
                title,
                isActive,
            })
        })
    }

    // Если нет сегментов, не рендерим
    if (segments.length === 0) {
        return null
    }

    return (
        <Breadcrumb className={className}>
            <BreadcrumbList>
                {segments.map((segment, index) => (
                    <React.Fragment key={index}>
                        <BreadcrumbItem
                            className={index === 0 ? "hidden md:block" : ""}
                        >
                            <BreadcrumbPage>{segment.title}</BreadcrumbPage>
                        </BreadcrumbItem>

                        {index < segments.length - 1 && (
                            <BreadcrumbSeparator
                                className={index === 0 ? "hidden md:block" : ""}
                            />
                        )}
                    </React.Fragment>
                ))}
            </BreadcrumbList>
        </Breadcrumb>
    )
}
