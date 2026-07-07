import { Image, Pressable, ScrollView, StyleSheet, View } from "react-native";
import Text from "../text/Text";
import { Link, useRouter } from "expo-router";
import { colors } from "../../themes/colors";
import { useState } from "react";
import Svg, { Path } from "react-native-svg";
import {
  AntDesign,
  FontAwesome5,
  FontAwesome,
  MaterialCommunityIcons,
  Ionicons,
  Feather,
} from "@expo/vector-icons";

const SidebarNav = ({ handleMenuClose }) => {
  const [menuItems, setMenuItems] = useState([
    {
      label: "Home",
      iconSrc: <AntDesign name="home" size={24} color="black" />,
      href: "/admin/dashboard",
    },
    {
      label: "Profile",
      iconSrc: <FontAwesome5 name="user-circle" size={24} color="black" />,
      href: "/admin/profile",
    },
    {
      label: "Warehouse",
      iconSrc: (
        <MaterialCommunityIcons name="warehouse" size={24} color="black" />
      ),
      href: "/admin/warehouse",
    },
    {
      label: "Product",
      iconSrc: <FontAwesome5 name="product-hunt" size={24} color="black" />,
      subMenuItems: [
        {
          label: "Product",
          href: "/admin/product",
        },
        {
          label: "Product Category",
          href: "/admin/product/category",
        },
        {
          label: "Brand",
          href: "/admin/product/brand",
        },
        {
          label: "Manufacturer",
          href: "/admin/product/manufacture",
        },
      ],
      isOpen: false,
    },
    {
      label: "Catalog",
      iconSrc: <Ionicons name="book-outline" size={24} color="black" />,
      subMenuItems: [
        {
          label: "Weight Unit",
          href: "/admin/catalog/weight-unit",
        },
        {
          label: "Measurement Unit",
          href: "/admin/catalog/measurement-unit",
        },
        {
          label: "Attribute",
          href: "/admin/catalog/attribute",
        },
      ],
      isOpen: false,
    },
    {
      label: "Pos invoice manager",
      iconSrc: <AntDesign name="filetext1" size={24} color="black" />,
      href: "/admin/pos-invoice",
    },
    {
      label: "Sales Return",
      iconSrc: <MaterialCommunityIcons name="sale" size={24} color="black" />,
      subMenuItems: [
        {
          label: "Sale return",
          href: "/admin/sales/return",
        },
        {
          label: "Sale return list",
          href: "/admin/sales/return-list",
        },
        {
          label: "Return request",
          href: "/admin/sales/return-request",
        },
      ],
      isOpen: false,
    },
    {
      label: "Purchase",
      iconSrc: <Feather name="shopping-bag" size={24} color="black" />,
      subMenuItems: [
        {
          label: "Purchase",
          href: "/admin/purchase",
        },
        {
          label: "Purchase receive list",
          href: "/admin/purchase/receive/list",
        },
        {
          label: "Purchase return list",
          href: "/admin/purchase/return/list",
        },
      ],
      isOpen: false,
    },
    {
      label: "Marketing",
      iconSrc: <FontAwesome name="bullhorn" size={24} color="black" />,
      subMenuItems: [
        {
          label: "Coupon",
          href: "/admin/coupon",
        },
      ],
      isOpen: false,
    },
    {
      label: "Customer",
      iconSrc: <FontAwesome name="users" size={24} color="black" />,
      href: "/admin/customer",
    },
    {
      label: "Suppliers",
      iconSrc: <FontAwesome name="users" size={24} color="black" />,
      href: "/admin/supplier",
    },
    {
      label: "Expenses",
      iconSrc: <FontAwesome5 name="money-bill-alt" size={24} color="black" />,
      subMenuItems: [
        {
          label: "Expenses category",
          href: "/admin/expense/category",
        },
        {
          label: "Expenses",
          href: "/admin/expense",
        },
      ],
      isOpen: false,
    },

    {
      label: "Reports",
      iconSrc: (
        <MaterialCommunityIcons
          name="file-multiple-outline"
          size={24}
          color="black"
        />
      ),
      subMenuItems: [
        {
          label: "Expense Report",
          href: "/admin/report/expense",
        },
        {
          label: "Sales report",
          href: "/admin/report/sales",
        },
        {
          label: "Purchases report",
          href: "/admin/report/purchase",
        },
        {
          label: "Payments report",
          href: "/admin/report/payment",
        },

        {
          label: "Warehouse Stock Report",
          href: "/admin/report/warehouse-stock",
        },
        {
          label: "Loss profit report",
          href: "/admin/report/loss-profit",
        },
      ],
      isOpen: false,
    },
    {
      label: "Settings",
      iconSrc: <AntDesign name="setting" size={24} color="black" />,
      subMenuItems: [
        {
          label: "Settings",
          href: "/admin/setting",
        },
        {
          label: "country",
          href: "/admin/setting/country",
        },
        {
          label: "State",
          href: "/admin/setting/state",
        },
        {
          label: "City",
          href: "/admin/setting/city",
        },
      ],
      isOpen: false,
    },
    // Add more menu items as needed
  ]);
  //toggle menu
  const toggleSubMenu = (index) => {
    const updatedMenuItems = menuItems.map((item, i) => ({
      ...item,
      isOpen: i === index ? !item.isOpen : false,
    }));
    setMenuItems(updatedMenuItems);
  };
  const router = useRouter();

  //handle navigate & sidebar close
  const handleNavigate = (href) => {
    router.push(href);
    handleMenuClose();
  };
  return (
    <View style={styles.sidebarWrapper}>
      <View style={styles.logoWrap}>
        <Image
          source={require("../../assets/images/logo.png")}
          style={styles.logo}
        />
      </View>
      <ScrollView style={styles.menuWrapper}>
        <View>
          {menuItems?.map((item, index) => (
            <View key={index}>
              <Pressable
                style={styles.menuLink}
                onPress={() => {
                  if (item.href) {
                    router.push(item.href);
                    handleMenuClose();
                  } else {
                    toggleSubMenu(index);
                  }
                }}
              >
                <Text>{item.iconSrc}</Text>
                <Text preset="h2_sb">{item?.label}</Text>
                {item?.subMenuItems && (
                  <View style={styles.subMenuArrow}>
                    <Svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                    >
                      <Path
                        d="M6 9L12 15L18 9"
                        stroke="#4F5B6D"
                        strokeWidth="2"
                        strokeLinecap="round"
                        strokeLinejoin="round"
                      />
                    </Svg>
                  </View>
                )}
              </Pressable>

              {item?.isOpen && (
                <View>
                  {item?.subMenuItems?.map((subMenuItem, subIndex) => (
                    <Pressable
                      style={[styles.subMenu, { paddingVertical: 5 }]}
                      key={subIndex}
                      onPress={() => handleNavigate(subMenuItem.href)}
                    >
                      <Text preset="h3">{subMenuItem.label}</Text>
                    </Pressable>
                  ))}
                </View>
              )}
            </View>
          ))}
        </View>
      </ScrollView>
    </View>
  );
};

export default SidebarNav;

const styles = StyleSheet.create({
  sidebarWrapper: {
    flex: 1,
    backgroundColor: colors.white,
    position: "absolute",
    height: "100%",
    zIndex: 2,
    top: 0,
    left: 0,
    width: 275,
  },
  logoWrap: {
    paddingVertical: 30,
    borderBottomColor: colors.lineBorder,
    borderBottomWidth: 1,
    width: "100%",
    paddingHorizontal: 20,
  },
  logo: {
    width: 180,
    height: 40,
    objectFit: "contain",
  },
  menuWrapper: {
    paddingHorizontal: 20,
  },
  menuLink: {
    alignItems: "center",
    paddingVertical: 20,
    borderBottomColor: colors.lineBorder,
    borderBottomWidth: 1,
    width: "100%",
    flexDirection: "row",
    gap: 12,
    position: "relative",
  },
  subMenuArrow: {
    position: "absolute",
    right: 0,
    top: 20,
  },
  subMenu: {
    paddingLeft: 20,
    paddingVertical: 5,
  },
});
