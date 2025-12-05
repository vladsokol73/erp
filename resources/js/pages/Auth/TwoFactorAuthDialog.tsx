import { useRef, useState } from "react"
import { useRoute } from "ziggy-js"
import { router } from "@inertiajs/react"
import axios, { AxiosResponse } from "axios"
import { LockKeyhole } from "lucide-react"
import { toast } from "sonner"

import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogFooter,
} from "@/components/ui/dialog"
import { Button } from "@/components/ui/button"
import { InputOTP, InputOTPSlot, InputOTPGroup, InputOTPSeparator } from "@/components/ui/input-otp"

interface TwoFactorAuthDialogProps {
    isOpen: boolean
    onOpenChange: (open: boolean) => void
}

interface TwoFactorResponse {
    success: boolean
    message?: string
}

export default function TwoFactorAuthDialog({ isOpen, onOpenChange }: TwoFactorAuthDialogProps) {
    const route = useRoute()
    const params = new URLSearchParams(typeof window !== 'undefined' ? window.location.search : '')
    const redirectParam = params.get('redirect')
    const redirect = redirectParam ? decodeURIComponent(redirectParam) : null
    const [twoFactorCode, setTwoFactorCode] = useState("")
    const [isVerifying, setIsVerifying] = useState(false)
    const [verificationResult, setVerificationResult] = useState<boolean | undefined>(undefined)
    const inputRef = useRef<HTMLInputElement>(null)
    const closeButtonRef = useRef<HTMLButtonElement>(null)
    const handleTwoFactorAuth = async () => {
        if (twoFactorCode.length !== 6) return

        setIsVerifying(true)

        try {
            const response: AxiosResponse<TwoFactorResponse> = await axios.post(
                route('2fa.verify'),
                {
                    code: twoFactorCode
                }
            )

            if (response.data.success) {
                setVerificationResult(true)
                setTimeout(() => {
                    handleDialogClose(false)
                    if (redirect) {
                        window.location.assign(redirect)
                    } else {
                    router.visit(route('home'))
                    }
                }, 1500)
            } else {
                setVerificationResult(false)
                setTwoFactorCode("")
            }
        } catch (error) {
            setVerificationResult(false)
            setTwoFactorCode("")
            console.error('2FA verification error:', error)
        } finally {
            setIsVerifying(false)
        }
    }

    const handleOtpComplete = async () => {
        await handleTwoFactorAuth()
    }
    const handleDialogClose = (open: boolean) => {
        if (!open) {
            setTwoFactorCode("")
            setVerificationResult(undefined)
        }
        onOpenChange(open)
    }

    return (
        <Dialog open={isOpen} onOpenChange={handleDialogClose}>
            <DialogContent className="sm:max-w-md">
                <div className="flex flex-col items-center gap-2">
                    <div
                        className="flex size-11 shrink-0 items-center justify-center rounded-full border"
                        aria-hidden="true"
                    >
                        <LockKeyhole className="size-4"/>
                    </div>
                    <DialogHeader>
                        <DialogTitle className="sm:text-center">
                            2FA
                        </DialogTitle>
                        <DialogDescription className="sm:text-center">
                            Enter code
                        </DialogDescription>
                    </DialogHeader>
                </div>

                <div className="space-y-4">
                    <div className="flex justify-center">
                        <InputOTP
                            id="verification-code"
                            ref={inputRef}
                            value={twoFactorCode}
                            onChange={setTwoFactorCode}
                            containerClassName="flex items-center gap-3 has-disabled:opacity-50"
                            maxLength={6}
                            onFocus={() => setVerificationResult(undefined)}
                            onComplete={handleOtpComplete}
                            disabled={isVerifying}
                        >
                            <InputOTPGroup>
                                <InputOTPSlot index={0} />
                            </InputOTPGroup>
                            <InputOTPGroup>
                                <InputOTPSlot index={1} />
                            </InputOTPGroup>
                            <InputOTPGroup>
                                <InputOTPSlot index={2} />
                            </InputOTPGroup>
                            <span className="mx-1"/>
                            <InputOTPGroup>
                                <InputOTPSlot index={3} />
                            </InputOTPGroup>
                            <InputOTPGroup>
                                <InputOTPSlot index={4} />
                            </InputOTPGroup>
                            <InputOTPGroup>
                                <InputOTPSlot index={5} />
                            </InputOTPGroup>
                        </InputOTP>
                    </div>

                    {verificationResult === false && (
                        <p
                            className="text-destructive text-center text-xs"
                            role="alert"
                            aria-live="polite"
                        >
                            Invalid verification code. Please try again.
                        </p>
                    )}
                </div>

                <DialogFooter>
                    <p className="text-xs text-default-500 text-center">
                        Open Google Authenticator app and enter the 6-digit code for this account
                    </p>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    )
}
