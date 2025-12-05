import { useState, useEffect, useRef } from 'react';
import axios from 'axios';

import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { Spinner } from "@/components/ui/spinner";
import { Button } from "@/components/ui/button";

interface DownloadDialogProps {
    isOpen: boolean;
    onClose: () => void;
    title?: string;
    creativeUrl: string;
    onDownloadComplete?: (downloadUrl: string) => void;
}

const DownloadDialog = ({
                            isOpen,
                            onClose,
                            title = "Download Creative",
                            creativeUrl,
                            onDownloadComplete
                        }: DownloadDialogProps) => {
    const [progress, setProgress] = useState(0);
    const [stage, setStage] = useState("Initializing...");
    const [taskCode, setTaskCode] = useState<string>("");
    const [downloadLink, setDownloadLink] = useState<string>("");
    const [error, setError] = useState<string | null>(null);
    const [loading, setLoading] = useState(false);

    const intervalRef = useRef<number | null>(null);

    // Старт загрузки
    const startDownload = async () => {
        setLoading(true);
        setError(null);
        setDownloadLink("");
        setProgress(0);
        setStage("Starting download...");

        try {
            const { data } = await axios.post("https://services.investingindigital.com/api/unic/upload", {
                url: creativeUrl
            });

            const taskCode = data?.task_code;
            if (!taskCode) {
                throw new Error("Invalid response: task_code not found");
            }

            setTaskCode(taskCode);
            startStatusCheck(taskCode);
        } catch (err: any) {
            console.error("Upload error:", err);
            setError(err?.response?.data?.message || err?.message || "Unknown error occurred");
            setStage("Failed to start download");
            setLoading(false);
        }
    };

    // Проверка статуса
    const checkStatus = async (code: string) => {
        try {
            const { data } = await axios.get(`https://services.investingindigital.com/api/unic/status/${code}`);

            setError(null);
            setStage(data.stage || "Processing");
            setProgress(data.progress || 0);

            if (data.state === "COMPLETED") {
                if (intervalRef.current) {
                    clearInterval(intervalRef.current);
                    intervalRef.current = null;
                }

                const downloadUrl = `https://services.investingindigital.com/api/unic/download/${code}`;
                setDownloadLink(downloadUrl);
                setStage("Download completed");
                setProgress(100);
                setLoading(false);

                if (onDownloadComplete) {
                    onDownloadComplete(downloadUrl);
                }
            }
        } catch (err: any) {
            setError(err?.response?.data?.message || err?.message || "Error checking download status");
        }
    };

    // Интервал проверок
    const startStatusCheck = (code: string) => {
        if (intervalRef.current) {
            clearInterval(intervalRef.current);
        }

        intervalRef.current = window.setInterval(() => checkStatus(code), 5000);
    };

    // Автостарт при открытии
    useEffect(() => {
        if (isOpen && creativeUrl) {
            startDownload().then();
        }

        return () => {
            if (intervalRef.current) {
                clearInterval(intervalRef.current);
                intervalRef.current = null;
            }
        };
    }, [isOpen, creativeUrl]);

    return (
        <Dialog open={isOpen} onOpenChange={(open) => !open && onClose()}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{title}</DialogTitle>
                </DialogHeader>
                <div className="flex flex-col gap-6">
                    {error ? (
                        <div className="flex flex-col items-center gap-4 py-4">
                            <p className="text-red-500">Error: {error}</p>
                            <Button onClick={startDownload}>Retry</Button>
                        </div>
                    ) : downloadLink ? (
                        <div className="flex flex-col items-center gap-4 py-4">
                            <p className="text-center text-green">Download Ready!</p>
                            <a
                                href={downloadLink}
                                className="px-4 py-2 bg-primary text-primary-foreground rounded-md"
                                download
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                Download File
                            </a>
                        </div>
                    ) : (
                        <span className="py-12 flex justify-center">
                            <Spinner size="large" />
                        </span>
                    )}

                    <div className="flex flex-col gap-2">
                        <div className="flex items-center gap-1 text-sm justify-center">
                            <span className="font-bold">Stage:</span>
                            <span className="text-muted-foreground">{stage}</span>
                        </div>
                        <div className="flex items-center gap-1 text-sm justify-center">
                            <span className="font-bold">Progress:</span>
                            <span className="text-muted-foreground">{progress}%</span>
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    );
};

export default DownloadDialog;
