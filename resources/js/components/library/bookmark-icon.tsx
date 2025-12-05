import React from 'react';
import { Bookmark } from 'lucide-react';
import { cn } from '@/lib/utils'; //

interface BookmarkIconProps {
    selected?: boolean;
    onSelectedChange?: (selected: boolean) => void;
    className?: string;
}

const BookmarkIcon: React.FC<BookmarkIconProps> = ({
                                                       selected = false,
                                                       onSelectedChange,

                                                       className,
                                                   }) => {
    const handleClick = () => {
        if (onSelectedChange) {
            onSelectedChange(!selected);
        }
    };

    return (
        <div
            className={cn(
                "inline-flex cursor-pointer",
                className
            )}
            onClick={handleClick}
            role="button"
            aria-label={selected ? "Delete favorites" : "Add to favorites"}
            tabIndex={0}
        >
            <Bookmark size={24} className={`transition-all duration-500 ` + (selected ? 'fill-red text-red' : 'text-muted-foreground hover:text-red')}  />
        </div>
    );
};

export default BookmarkIcon;
