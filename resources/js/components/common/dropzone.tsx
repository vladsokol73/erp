import { useDropzone } from 'react-dropzone'
import { useCallback, useState } from 'react'
import { cn } from '@/lib/utils'
import { Card, CardContent } from '@/components/ui/card'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import { UploadCloud, Loader2, XCircle, Trash2 } from 'lucide-react'
import { usePage } from '@inertiajs/react'
import type { Accept } from 'react-dropzone'

type FileStatus = 'pending' | 'uploading' | 'done' | 'error'

type UploadingFile = {
    file: File
    progress: number
    status: FileStatus
    response?: any
}

type DropzoneProps = {
    maxFiles?: number
    accept?: Accept
    uploadUrl: string
    onRemove?: (args: { file: File; response?: any }) => Promise<void>
    onUploaded?: (args: { file: File; response: any }) => void
}

export function Dropzone({
                             maxFiles = 10,
                             accept,
                             uploadUrl,
                             onRemove,
                             onUploaded,
                         }: DropzoneProps) {
    const [files, setFiles] = useState<UploadingFile[]>([])
    const csrf_token = usePage().props.csrf_token as string

    const uploadFile = async (fileObj: UploadingFile, index: number) => {
        const formData = new FormData()
        formData.append('file', fileObj.file)

        const mime = fileObj.file.type
        const type = mime.startsWith('image')
            ? 'image'
            : mime.startsWith('video')
                ? 'video'
                : 'unknown'

        if (type === 'unknown') {
            console.warn('Unsupported file type:', mime)
            return
        }

        formData.append('type', type)

        try {
            setFiles((prev) =>
                prev.map((f, i) =>
                    i === index ? { ...f, status: 'uploading' } : f
                )
            )

            const xhr = new XMLHttpRequest()

            xhr.upload.onprogress = (event) => {
                if (event.lengthComputable) {
                    const percent = (event.loaded / event.total) * 100
                    setFiles((prev) =>
                        prev.map((f, i) =>
                            i === index ? { ...f, progress: percent } : f
                        )
                    )
                }
            }

            xhr.onload = () => {
                const isSuccess = xhr.status === 200
                const parsed = isSuccess ? safeParseJSON(xhr.responseText) : null

                setFiles((prev) =>
                    prev.map((f, i) =>
                        i === index
                            ? {
                                ...f,
                                status: isSuccess ? 'done' : 'error',
                                response: parsed,
                            }
                            : f
                    )
                )

                if (isSuccess && parsed && typeof onUploaded === 'function') {
                    onUploaded({
                        file: fileObj.file,
                        response: {
                            ...parsed,
                            type,
                        },
                    })
                }
            }

            xhr.onerror = () => {
                setFiles((prev) =>
                    prev.map((f, i) =>
                        i === index ? { ...f, status: 'error' } : f
                    )
                )
            }

            xhr.open('POST', uploadUrl)
            xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token)
            xhr.send(formData)
        } catch {
            setFiles((prev) =>
                prev.map((f, i) =>
                    i === index ? { ...f, status: 'error' } : f
                )
            )
        }
    }

    const onDrop = useCallback(
        (acceptedFiles: File[]) => {
            const newFiles: UploadingFile[] = acceptedFiles
                .slice(0, maxFiles)
                .map((file) => ({
                    file,
                    progress: 0,
                    status: 'pending',
                }))
            const limited = [...files, ...newFiles].slice(0, maxFiles)
            setFiles(limited)

            newFiles.forEach((fileObj, i) => {
                uploadFile(fileObj, files.length + i)
            })
        },
        [files, maxFiles, uploadUrl]
    )

    const handleRemove = async (file: File) => {
        const fileObj = files.find((f) => f.file === file)
        if (!fileObj || !onRemove) return

        try {
            await onRemove({ file: fileObj.file, response: fileObj.response })
            setFiles((prev) => prev.filter((f) => f.file !== file))
        } catch (err) {
            console.error('Failed to delete file:', err)
        }
    }

    const { getRootProps, getInputProps, isDragActive, fileRejections } =
        useDropzone({
            onDrop,
            multiple: true,
            maxFiles,
            accept,
        })

    return (
        <Card className="w-full">
            <CardContent
                {...getRootProps()}
                className={cn(
                    'mx-6 p-6 border-2 border-dashed rounded-xl text-center cursor-pointer transition-colors',
                    isDragActive ? 'border-primary bg-accent/40' : 'border-muted'
                )}
            >
                <input {...getInputProps()} />
                <div className="flex flex-col items-center space-y-2 px-6">
                    <UploadCloud className="h-8 w-8 text-muted-foreground" />
                    <p className="text-sm text-muted-foreground">
                        Drag & drop files here, or click to select
                    </p>
                </div>
            </CardContent>

            {files.length > 0 && (
                <div className="p-4 space-y-2">
                    <div className="bg-background overflow-hidden rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow className="bg-muted/50">
                                    <TableHead className="h-9 py-2">File name</TableHead>
                                    <TableHead className="h-9 py-2 text-right">Size</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {files.map((fileObj, index) => (
                                    <TableRow key={index}>
                                        <TableCell className="py-2 font-medium">
                                            <div className="flex items-center gap-2">
                                                {fileObj.status === 'uploading' && (
                                                    <Loader2 className="h-4 w-4 animate-spin text-muted-foreground" />
                                                )}
                                                {fileObj.status === 'error' && (
                                                    <XCircle className="h-4 w-4 text-destructive" />
                                                )}

                                                {fileObj.status === 'done' && (
                                                    <button
                                                        type="button"
                                                        onClick={() => handleRemove(fileObj.file)}
                                                        className="text-muted-foreground hover:text-destructive transition-colors"
                                                        title="Delete"
                                                    >
                                                        <Trash2 className="h-4 w-4" />
                                                    </button>
                                                )}

                                                <span
                                                    className={cn({
                                                        'text-destructive': fileObj.status === 'error',
                                                    })}
                                                >
                                                    {fileObj.file.name}
                                                </span>
                                            </div>
                                        </TableCell>
                                        <TableCell className="py-2 text-right">
                                            {(fileObj.file.size / 1024).toFixed(1)} KB
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </div>
                </div>
            )}

            {fileRejections.length > 0 && (
                <div className="p-4 text-sm text-destructive">
                    Some files were rejected due to restrictions.
                </div>
            )}
        </Card>
    )
}

function safeParseJSON(data: string | null): any {
    try {
        return data ? JSON.parse(data) : undefined
    } catch {
        return undefined
    }
}
