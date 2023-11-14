function useAutocompleteID(list, value) {
    return list.filter((cs) => cs.id == value)[0].id;
}
export { useAutocompleteID };
