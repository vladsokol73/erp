"use client"
import React, { useState, useRef } from 'react';
import ReactPlayer from 'react-player/lazy';
import { Play } from 'lucide-react';
import { cn } from "@/lib/utils";

interface VideoPreviewProps {
    url: string;
    thumbnailUrl?: string | null;
    className?: string;
    fullWidth?: boolean;
    fullHeight?: boolean;
    aspectRatio?: string;
    fillMode?: 'cover' | 'contain';
    onPlay?: () => void;
    openLightbox?: () => void;
}

export default function VideoPreview({
                                         url,
                                         thumbnailUrl,
                                         className,
                                         fullWidth = true,
                                         fullHeight = true,
                                         aspectRatio = "16/9",
                                         fillMode = 'cover',
                                         onPlay,
                                         openLightbox
                                     }: VideoPreviewProps) {
    const [isHovering, setIsHovering] = useState(false);
    const [hasStartedOnce, setHasStartedOnce] = useState(false);
    const [shouldRenderPlayer, setShouldRenderPlayer] = useState(false);
    const [progress, setProgress] = useState(0);
    const playerRef = useRef<ReactPlayer>(null);

    const handleMouseEnter = () => {
        setIsHovering(true);
        setShouldRenderPlayer(true);
    };

    const handleMouseLeave = () => {
        setIsHovering(false);
        if (playerRef.current && hasStartedOnce) {
            playerRef.current.seekTo(0);
        }
    };

    const handleClick = () => {
        if (openLightbox) openLightbox();
        if (onPlay) onPlay();
    };

    return (
        <div
            className={cn(
                "relative rounded-md overflow-hidden group cursor-pointer bg-black flex items-center justify-center",
                fullWidth && "w-full",
                fullHeight && "h-full",
                className
            )}
            style={{ aspectRatio: fullHeight ? undefined : aspectRatio }}
            onMouseEnter={handleMouseEnter}
            onMouseLeave={handleMouseLeave}
            onClick={handleClick}
        >
            {shouldRenderPlayer && (
                <div className={cn(
                    "w-full h-full flex items-center justify-center overflow-hidden",
                    fillMode === 'cover' && "absolute inset-0"
                )}>
                    <ReactPlayer
                        thumbnail={thumbnailUrl}
                        ref={playerRef}
                        url={url}
                        width={fillMode === 'cover' ? "100%" : "auto"}
                        height={fillMode === 'cover' ? "100%" : "auto"}
                        playing={isHovering}
                        muted={true}
                        volume={0}
                        controls={false}
                        playbackRate={1.5}
                        onStart={() => setHasStartedOnce(true)}
                        progressInterval={100}
                        onProgress={(state) => setProgress(state.played)}
                        config={{
                            file: {
                                attributes: {
                                    controlsList: 'nodownload',
                                    disablePictureInPicture: true
                                }
                            },
                            youtube: {
                                playerVars: {
                                    showinfo: 0,
                                    rel: 0,
                                    iv_load_policy: 3,
                                    modestbranding: 1
                                }
                            },
                            vimeo: {
                                playerOptions: {
                                    autopause: 0,
                                    byline: 0,
                                    portrait: 0,
                                    title: 0
                                }
                            }
                        }}
                        style={{
                            pointerEvents: 'none',
                            objectFit: fillMode,
                            minWidth: fillMode === 'cover' ? '100%' : 'auto',
                            minHeight: fillMode === 'cover' ? '100%' : 'auto',
                        }}
                    />
                </div>
            )}

            <div
                className={cn(
                    "absolute inset-0 w-full h-full transition-opacity duration-1500",
                    isHovering ? "opacity-0" : "opacity-100"
                )}
            >
                {thumbnailUrl ? (
                    <img
                        src={thumbnailUrl}
                        alt="Video thumbnail"
                        className="absolute inset-0 w-full h-full object-cover"
                        style={{ objectPosition: 'center' }}
                    />
                ) : (
                    <ReactPlayer
                        url={url}
                        playing={false}
                        controls={false}
                        muted
                        width="100%"
                        height="100%"
                        config={{
                            file: {
                                attributes: {
                                    controlsList: 'nodownload',
                                    disablePictureInPicture: true
                                }
                            }
                        }}
                        style={{
                            position: 'absolute',
                            top: 0,
                            left: 0,
                            width: '100%',
                            height: '100%',
                            objectFit: 'cover',
                            objectPosition: 'center',
                            pointerEvents: 'none'
                        }}
                    />
                )}
            </div>

            {/* Иконка Play */}
            <div
                className={cn(
                    "absolute inset-0 bg-black/40 flex items-center justify-center cursor-zoom-in transition-opacity duration-500",
                    isHovering ? "opacity-100" : "opacity-0"
                )}
            >
                <Play size={48} className="text-white" />
            </div>

            {/* Progress bar */}
            {isHovering && shouldRenderPlayer && (
                <div className="absolute bottom-0 left-0 right-0 h-1 bg-muted">
                    <div
                        className="h-full bg-foreground duration-200"
                        style={{ width: `${progress * 100}%` }}
                    />
                </div>
            )}
        </div>
    );
}
