// Тип для комментария
export interface Comment {
    id: string | number;
    author: {
        name: string;
        avatarFallback: string;
    };
    date: string;
    content: string;
}

// Тип для медиа-элемента
export interface MediaItem {
    id: string | number;
    country: {
        name: string;
        flagUrl: string;
    };
    date: string;
    tags: string[];
    mediaType: "image" | "video";
    mediaSrc: string;
    code: string;
    likeCount: number;
    dislikeCount: number;
    hasDownloadVariants: boolean;
    comments: Comment[];
}

// Пропсы для компонента FilterPanel
export interface FilterPanelProps {
    initialOpen?: boolean;
}

// Пропсы для компонента SortDropdown
export interface SortDropdownProps {
    initialSortType?: string;
    onSortChange?: (value: string) => void;
}

// Пропсы для компонента MediaPreview
export interface MediaPreviewProps {
    type: "image" | "video";
    src: string;
    onPreviewOpen: () => void;
}

// Пропсы для компонента MediaCardHeader
export interface MediaCardHeaderProps {
    country: {
        name: string;
        flagUrl: string;
    };
    date: string;
    tags: string[];
    commentCount: number;
    onCommentsClick?: () => void;
}

// Пропсы для компонента LikeDislikeButtons
export interface LikeDislikeButtonsProps {
    likeCount: number;
    dislikeCount: number;
    onLike?: () => void;
    onDislike?: () => void;
}

// Пропсы для компонента MediaCardFooter
export interface MediaCardFooterProps {
    code: string;
    likeCount: number;
    dislikeCount: number;
    hasDownloadVariants?: boolean;
    onOriginalDownload?: () => void;
    onUniqueDownload?: () => void;
    onLike?: () => void;
    onDislike?: () => void;
}

// Пропсы для компонента CommentItem
export interface CommentItemProps {
    author: {
        name: string;
        avatarFallback: string;
    };
    date: string;
    content: string;
}

// Пропсы для компонента CommentList
export interface CommentListProps {
    comments: Comment[];
}

// Пропсы для компонента CommentDialog
export interface CommentDialogProps {
    isOpen: boolean;
    onClose: () => void;
    comments: Comment[];
    onAddComment: (comment: string) => void;
}

// Пропсы для компонента MediaCard
export interface MediaCardProps {
    id: string | number;
    country: {
        name: string;
        flagUrl: string;
    };
    date: string;
    tags: string[];
    mediaType: "image" | "video";
    mediaSrc: string;
    code: string;
    likeCount: number;
    dislikeCount: number;
    hasDownloadVariants?: boolean;
    comments: Comment[];
    onPreviewOpen: () => void;
    onAddComment?: (id: string | number, comment: string) => void;
    onLike?: (id: string | number) => void;
    onDislike?: (id: string | number) => void;
    onOriginalDownload?: (id: string | number) => void;
    onUniqueDownload?: (id: string | number) => void;
}

// Типы для слайдов изображений
export interface ImageSlide {
    src: string;
    alt?: string;
}

// Типы для слайдов видео
export interface VideoSlide {
    type: "video";
    sources: {
        src: string;
        type: string;
    }[];
    poster?: string;
}

// Тип данных Креативов


// Интерфейс для структуры комментария
export interface CommentCreative {
    comment: string;
    created_at: string;
    id: number;
    user_name: string;
}

// Интерфейс для структуры страны
export interface CountryCreative {
    id: number;
    name: string;
    iso: string;
    img: string;
}

// Интерфейс для структуры тега
export interface TagCreative {
    id: number;
    name: string;
    style: string | null;
}

export interface UserCreative {
    id: number;
    name: string;
}

export interface LikeCreative {
    id: number;
    value: number;
}

// Основной интерфейс для структуры данных
export interface Creative {
    code: string;
    comments: CommentCreative[];
    country: CountryCreative;
    created_at: string;
    id: number;
    likes_count: number;
    dislikes_count: number;
    user_liked: boolean;
    user_disliked: boolean;
    resolution: string;
    tags: TagCreative[];
    type: "image" | "video";
    url: string;
    thumbnail: string | null;
    user_id: number;
    favorite: boolean;
}

export interface CreativeData {
    currentPage: number
    items: Creative[]
    lastPage: number
    perPage: number
    total: number
}

export interface FilterValues {
    countries: string[];
    users: string[];
    tags: string[];
    types: string[];
}
