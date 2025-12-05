import React from "react";
import SearchInput from "@/components/common/input/search-input";

interface TicketSearchInputProps {
    defaultValue: string;
    onSubmit: (search: string) => void;
}

const TicketSearchInput = ({ defaultValue, onSubmit }: TicketSearchInputProps) => {
    return (
        <SearchInput
            placeholder="Enter keyword..."
            defaultValue={defaultValue}
            onChangeDebounced={onSubmit}
        />
    );
};

export default TicketSearchInput;
