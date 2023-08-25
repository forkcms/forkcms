export class ArrayUtil {
  /**
   * Is the given value present in the array
   *
   * @return bool
   */
  static inArray (needle, array) {
    // loop values
    for (const i in array) {
      if (array[i] === needle) return true
    }

    // fallback
    return false
  }
}
