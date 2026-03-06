import React, { useEffect, useState } from 'react';
import { StyleSheet, View, Text, TouchableOpacity, Alert, TextInput, ScrollView } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import * as SecureStore from 'expo-secure-store';
import { useRouter } from 'expo-router';
import { getCategories, addCategory } from '../../lib/api';
import { SafeAreaView } from 'react-native-safe-area-context';

export default function SettingsScreen() {
    const [instName, setInstName] = useState('');
    const [categories, setCategories] = useState<any[]>([]);
    const [newCat, setNewCat] = useState('');
    const [loading, setLoading] = useState(false);
    const router = useRouter();

    useEffect(() => {
        SecureStore.getItemAsync('institute_name').then(name => setInstName(name || 'Institute'));
        loadCategories();
    }, []);

    const loadCategories = async () => {
        const resp = await getCategories();
        if (resp.status) setCategories(resp.data);
    };

    const handleAddCategory = async () => {
        if (!newCat) return;
        setLoading(true);
        const resp = await addCategory({ category_name: newCat });
        if (resp.status) {
            setNewCat('');
            loadCategories();
            Alert.alert('Success', 'Category added');
        }
        setLoading(false);
    };

    const handleLogout = async () => {
        Alert.alert('Logout', 'Are you sure you want to sign out?', [
            { text: 'Cancel', style: 'cancel' },
            {
                text: 'Logout',
                style: 'destructive',
                onPress: async () => {
                    await SecureStore.deleteItemAsync('api_key');
                    await SecureStore.deleteItemAsync('institute_name');
                    router.replace('/login');
                }
            }
        ]);
    };

    return (
        <SafeAreaView style={styles.container} edges={['top']}>
            <ScrollView showsVerticalScrollIndicator={false}>
                <View style={styles.header}>
                    <View style={styles.profileBox}>
                        <View style={styles.avatar}>
                            <Ionicons name="business" size={40} color="white" />
                        </View>
                        <Text style={styles.instName}>{instName}</Text>
                        <Text style={styles.adminTag}>Super Administrator</Text>

                        <TouchableOpacity style={styles.manageBtn} onPress={() => router.push('/manage-classes')}>
                            <Ionicons name="school-outline" size={16} color="#dc2626" />
                            <Text style={styles.manageBtnText}>Manage Classes / Courses</Text>
                        </TouchableOpacity>
                    </View>
                </View>

                <View style={styles.content}>
                    <View style={styles.sectionCard}>
                        <Text style={styles.sectionTitle}>Fee Configuration</Text>
                        <View style={styles.addBox}>
                            <TextInput
                                style={styles.smallInput}
                                placeholder="Add new fee category..."
                                value={newCat}
                                onChangeText={setNewCat}
                            />
                            <TouchableOpacity style={styles.addBtn} onPress={handleAddCategory} disabled={loading}>
                                <Ionicons name="add" size={24} color="white" />
                            </TouchableOpacity>
                        </View>
                        <View style={styles.catTabs}>
                            {categories.map((c, i) => (
                                <View key={i} style={styles.catTab}>
                                    <Text style={styles.catTabText}>{c.category_name}</Text>
                                </View>
                            ))}
                        </View>
                    </View>

                    <View style={styles.sectionCard}>
                        <Text style={styles.sectionTitle}>Institute Settings</Text>
                        <TouchableOpacity style={styles.menuItem}>
                            <View style={[styles.iconBox, { backgroundColor: '#eff6ff' }]}>
                                <Ionicons name="document-text" size={20} color="#3b82f6" />
                            </View>
                            <Text style={styles.menuText}>Update Profile</Text>
                            <Ionicons name="chevron-forward" size={18} color="#cbd5e1" />
                        </TouchableOpacity>

                        <TouchableOpacity style={styles.menuItem}>
                            <View style={[styles.iconBox, { backgroundColor: '#fdf4ff' }]}>
                                <Ionicons name="shield-checkmark" size={20} color="#a855f7" />
                            </View>
                            <Text style={styles.menuText}>Security & PIN</Text>
                            <Ionicons name="chevron-forward" size={18} color="#cbd5e1" />
                        </TouchableOpacity>
                    </View>

                    <View style={styles.sectionCard}>
                        <TouchableOpacity style={styles.menuItem} onPress={handleLogout}>
                            <View style={[styles.iconBox, { backgroundColor: '#fef2f2' }]}>
                                <Ionicons name="log-out" size={20} color="#dc2626" />
                            </View>
                            <Text style={[styles.menuText, { color: '#dc2626' }]}>Sign Out</Text>
                            <Ionicons name="chevron-forward" size={18} color="#fecdd3" />
                        </TouchableOpacity>
                    </View>

                    <Text style={styles.versionStyle}>FeeFlow App v1.1.0 • Built with ♥</Text>
                </View>
            </ScrollView>
        </SafeAreaView>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#f8fafc',
    },
    header: {
        backgroundColor: '#fff',
        paddingBottom: 20,
        borderBottomLeftRadius: 30,
        borderBottomRightRadius: 30,
        elevation: 4,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 2 },
        shadowOpacity: 0.05,
        shadowRadius: 10,
    },
    profileBox: {
        padding: 30,
        alignItems: 'center',
    },
    avatar: {
        width: 90,
        height: 90,
        borderRadius: 45,
        backgroundColor: '#dc2626',
        alignItems: 'center',
        justifyContent: 'center',
        marginBottom: 15,
        borderWidth: 4,
        borderColor: '#fee2e2',
    },
    instName: {
        fontSize: 22,
        fontWeight: '800',
        color: '#1e293b',
    },
    adminTag: {
        fontSize: 14,
        color: '#64748b',
        marginTop: 4,
        fontWeight: '600',
    },
    manageBtn: {
        flexDirection: 'row',
        alignItems: 'center',
        marginTop: 15,
        backgroundColor: '#fff1f2',
        paddingHorizontal: 20,
        paddingVertical: 10,
        borderRadius: 25,
        borderWidth: 1,
        borderColor: '#fecdd3',
    },
    manageBtnText: {
        color: '#dc2626',
        fontWeight: '700',
        fontSize: 13,
        marginLeft: 8,
    },
    content: {
        padding: 20,
    },
    sectionCard: {
        backgroundColor: '#fff',
        borderRadius: 20,
        padding: 20,
        marginBottom: 20,
        elevation: 2,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 1 },
        shadowOpacity: 0.05,
        shadowRadius: 5,
    },
    sectionTitle: {
        fontSize: 14,
        fontWeight: '800',
        color: '#94a3b8',
        textTransform: 'uppercase',
        letterSpacing: 1,
        marginBottom: 15,
    },
    addBox: {
        flexDirection: 'row',
        gap: 10,
        marginBottom: 15,
    },
    smallInput: {
        flex: 1,
        backgroundColor: '#f8fafc',
        borderRadius: 12,
        paddingHorizontal: 15,
        height: 48,
        borderWidth: 1,
        borderColor: '#e2e8f0',
    },
    addBtn: {
        backgroundColor: '#dc2626',
        width: 48,
        height: 48,
        borderRadius: 12,
        alignItems: 'center',
        justifyContent: 'center',
    },
    catTabs: {
        flexDirection: 'row',
        flexWrap: 'wrap',
        gap: 8,
    },
    catTab: {
        backgroundColor: '#f1f5f9',
        paddingHorizontal: 12,
        paddingVertical: 6,
        borderRadius: 8,
        borderWidth: 1,
        borderColor: '#e2e8f0',
    },
    catTabText: {
        fontSize: 12,
        color: '#475569',
        fontWeight: '600',
    },
    menuItem: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingVertical: 12,
    },
    iconBox: {
        width: 40,
        height: 40,
        borderRadius: 10,
        alignItems: 'center',
        justifyContent: 'center',
        marginRight: 15,
    },
    menuText: {
        flex: 1,
        fontSize: 16,
        fontWeight: '700',
        color: '#334155',
    },
    versionStyle: {
        textAlign: 'center',
        marginTop: 10,
        color: '#94a3b8',
        fontSize: 12,
        fontWeight: '600',
    }
});
