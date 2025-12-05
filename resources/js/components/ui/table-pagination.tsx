"use client"

import { useId, useState } from "react"
import {
    ChevronLeftIcon,
    ChevronRightIcon,
    ChevronsLeftIcon,
    ChevronsRightIcon,
} from "lucide-react"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
    Pagination,
    PaginationContent,
    PaginationEllipsis,
    PaginationItem,
    PaginationLink,
} from "@/components/ui/pagination"

function usePagination({
                           currentPage,
                           totalPages,
                           paginationItemsToDisplay = 5,
                       }: {
    currentPage: number
    totalPages: number
    paginationItemsToDisplay?: number
}) {
    const leftSiblingCount = Math.floor(paginationItemsToDisplay / 2)
    const rightSiblingCount = paginationItemsToDisplay - leftSiblingCount - 1

    let startPage = Math.max(currentPage - leftSiblingCount, 1)
    let endPage = Math.min(currentPage + rightSiblingCount, totalPages)

    const pageShift = paginationItemsToDisplay - (endPage - startPage + 1)
    if (pageShift > 0) {
        if (startPage === 1) {
            endPage = Math.min(endPage + pageShift, totalPages)
        } else if (endPage === totalPages) {
            startPage = Math.max(startPage - pageShift, 1)
        }
    }

    const pages = Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i)

    return {
        pages,
        showLeftEllipsis: startPage > 1,
        showRightEllipsis: endPage < totalPages,
    }
}

interface TablePaginationProps {
    currentPage: number
    totalPages: number
    onPageChange: (page: number) => void
    paginationItemsToDisplay?: number
}

export function TablePagination({
                                    currentPage,
                                    totalPages,
                                    onPageChange,
                                    paginationItemsToDisplay = 5,
                                }: TablePaginationProps) {
    const id = useId()
    const [inputPage, setInputPage] = useState<string>(currentPage.toString())

    const { pages, showLeftEllipsis, showRightEllipsis } = usePagination({
        currentPage,
        totalPages,
        paginationItemsToDisplay,
    })

    const { pages: mobilePages } = usePagination({
        currentPage,
        totalPages,
        paginationItemsToDisplay: 3,
    })

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setInputPage(e.target.value.replace(/[^0-9]/g, ''))
    }

    const handleGoToPage = (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (e.key === 'Enter') {
            const pageNumber = parseInt(inputPage)
            if (!isNaN(pageNumber) && pageNumber >= 1 && pageNumber <= totalPages) {
                onPageChange(pageNumber)
            } else {
                setInputPage(currentPage.toString())
            }
        }
    }

    return (
        <div className="flex flex-col sm:flex-row items-center gap-4">
            {/* Desktop pagination */}
            <div className="hidden sm:block">
                <Pagination>
                    <PaginationContent>
                        {/* Go to first page */}
                        {currentPage > 1 && (
                            <PaginationItem>
                                <PaginationLink
                                    className="cursor-pointer"
                                    onClick={() => onPageChange(1)}
                                    role="button"
                                >
                                    <ChevronsLeftIcon size={16} />
                                </PaginationLink>
                            </PaginationItem>
                        )}

                        {/* Previous */}
                        <PaginationItem>
                            <PaginationLink
                                className="aria-disabled:pointer-events-none aria-disabled:opacity-50 cursor-pointer"
                                onClick={() => currentPage > 1 && onPageChange(currentPage - 1)}
                                aria-disabled={currentPage === 1}
                                role="button"
                            >
                                <ChevronLeftIcon size={16} />
                            </PaginationLink>
                        </PaginationItem>

                        {showLeftEllipsis && (
                            <PaginationItem>
                                <PaginationEllipsis />
                            </PaginationItem>
                        )}

                        {pages.map((page) => (
                            <PaginationItem key={page}>
                                <PaginationLink
                                    className="cursor-pointer !min-w-9 !w-auto !px-2"
                                    onClick={() => onPageChange(page)}
                                    isActive={page === currentPage}
                                    role="button"
                                >
                                    {page}
                                </PaginationLink>
                            </PaginationItem>
                        ))}

                        {showRightEllipsis && (
                            <PaginationItem>
                                <PaginationEllipsis />
                            </PaginationItem>
                        )}

                        {/* Next */}
                        <PaginationItem>
                            <PaginationLink
                                className="aria-disabled:pointer-events-none aria-disabled:opacity-50 cursor-pointer"
                                onClick={() => currentPage < totalPages && onPageChange(currentPage + 1)}
                                aria-disabled={currentPage === totalPages}
                                role="button"
                            >
                                <ChevronRightIcon size={16} />
                            </PaginationLink>
                        </PaginationItem>

                        {/* Go to last page */}
                        {currentPage < totalPages && (
                            <PaginationItem>
                                <PaginationLink
                                    className="cursor-pointer"
                                    onClick={() => onPageChange(totalPages)}
                                    role="button"
                                >
                                    <ChevronsRightIcon size={16} />
                                </PaginationLink>
                            </PaginationItem>
                        )}
                    </PaginationContent>
                </Pagination>
            </div>

            {/* Mobile pagination */}
            <div className="block sm:hidden w-full">
                <Pagination>
                    <PaginationContent className="flex justify-center gap-1">
                        <PaginationItem>
                            <PaginationLink
                                className="aria-disabled:pointer-events-none aria-disabled:opacity-50 cursor-pointer"
                                onClick={() => onPageChange(1)}
                                aria-disabled={currentPage === 1}
                                role="button"
                            >
                                <ChevronsLeftIcon size={16} />
                            </PaginationLink>
                        </PaginationItem>

                        <PaginationItem>
                            <PaginationLink
                                className="aria-disabled:pointer-events-none aria-disabled:opacity-50 cursor-pointer"
                                onClick={() => currentPage > 1 && onPageChange(currentPage - 1)}
                                aria-disabled={currentPage === 1}
                                role="button"
                            >
                                <ChevronLeftIcon size={16} />
                            </PaginationLink>
                        </PaginationItem>

                        {mobilePages.map((page) => (
                            <PaginationItem key={page}>
                                <PaginationLink
                                    className="cursor-pointer"
                                    onClick={() => onPageChange(page)}
                                    isActive={page === currentPage}
                                    role="button"
                                >
                                    {page}
                                </PaginationLink>
                            </PaginationItem>
                        ))}

                        {totalPages > 3 && currentPage + 1 < totalPages && (
                            <PaginationItem>
                                <PaginationEllipsis />
                            </PaginationItem>
                        )}

                        <PaginationItem>
                            <PaginationLink
                                className="aria-disabled:pointer-events-none aria-disabled:opacity-50 cursor-pointer"
                                onClick={() => currentPage < totalPages && onPageChange(currentPage + 1)}
                                aria-disabled={currentPage === totalPages}
                                role="button"
                            >
                                <ChevronRightIcon size={16} />
                            </PaginationLink>
                        </PaginationItem>

                        <PaginationItem>
                            <PaginationLink
                                className="aria-disabled:pointer-events-none aria-disabled:opacity-50 cursor-pointer"
                                onClick={() => onPageChange(totalPages)}
                                aria-disabled={currentPage === totalPages}
                                role="button"
                            >
                                <ChevronsRightIcon size={16} />
                            </PaginationLink>
                        </PaginationItem>
                    </PaginationContent>
                </Pagination>
            </div>

            {/* Go to page input */}
            <div className="hidden sm:flex items-center gap-3">
                <Label htmlFor={id} className="whitespace-nowrap text-sm">
                    Go to page
                </Label>
                <Input
                    id={id}
                    type="text"
                    className="w-14"
                    value={inputPage}
                    onChange={handleInputChange}
                    onKeyDown={handleGoToPage}
                    min={1}
                    max={totalPages}
                />
                <span className="whitespace-nowrap text-muted-foreground text-sm">
                    of
                </span>
                <span className="whitespace-nowrap text-sm">
                    {totalPages}
                </span>
            </div>
        </div>
    )
}
