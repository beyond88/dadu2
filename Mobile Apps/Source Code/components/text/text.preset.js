import { colors } from "../../themes/colors";
import { typography } from "../../themes/typography";

const BASE = {
  fontFamily: typography.font_regular,
  fontSize: 12,
  color: colors.black,
};

const Medium = {
  fontFamily: typography.font_medium,
  color: colors.black,
};

const SemiBold = {
  fontFamily: typography.font_semibold,
  color: colors.black,
};

const Bold = {
  fontFamily: typography.font_bold,
  color: colors.black,
};

export const presets = {
  default: BASE,

  h1: {
    ...SemiBold,
    fontSize: 20,
  },
  h2: {
    ...Bold,
    fontSize: 15,
  },
  h2_m: {
    ...Medium,
    fontSize: 15,
  },
  h2_sb: {
    ...SemiBold,
    fontSize: 15,
  },
  h3: {
    ...SemiBold,
    fontSize: 14,
  },
  h3_r: {
    ...BASE,
    fontSize: 14,
  },
  h4: {
    ...BASE,
    fontSize: 12,
  },
  h5: {
    ...SemiBold,
    fontSize: 12,
  },
  h5_m: {
    ...Medium,
    fontSize: 12,
  },
  h6: {
    ...BASE,
    fontSize: 10,
  },
  h6_m: {
    ...Medium,
    fontSize: 10,
  },
  h6_s: {
    ...BASE,
    fontSize: 8,
  },
};
