import React, { useEffect, useCallback, useState } from 'react';
import { StyleSheet, View, Text, ScrollView, ActivityIndicator, TouchableOpacity, Alert } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { getFees, getStudents } from '../lib/api';
import { useLocalSearchParams, Stack, useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';

export default function StudentDetailsScreen() {
    const { id } = useLocalSearchParams();
    const router = useRouter();
    const [student, setStudent] = useState<any>(null);
    const [history, setHistory] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);
    const [totalPaid, setTotalPaid] = useState(0);

    const loadData = useCallback(async () => {
        try {
            const sResp = await getStudents();
            const found = sResp.data.find((s: any) => s.id.toString() === id);
            setStudent(found);

            const fResp = await getFees(id);
            if (fResp.status) {
                setHistory(fResp.data);
                const total = fResp.data.reduce((sum: number, item: any) => sum + parseFloat(item.amount), 0);
                setTotalPaid(total);
            }
        } catch (error) {
            console.error(error);
        } finally {
            setLoading(false);
        }
    }, [id]);

    useEffect(() => {
        loadData();
    }, [loadData]);

    if (loading) return <ActivityIndicator size="large" color="#dc2626" style={{ marginTop: 50 }} />;

    return (
        <SafeAreaView style={styles.container} edges={['bottom', 'left', 'right']}>
            <Stack.Screen options={{
                title: 'Student Profile',
                headerShown: true,
                headerStyle: { backgroundColor: '#fff' },
                headerRight: () => (
                    <TouchableOpacity onPress={() => router.push({ pathname: '/edit-student', params: { id } })}>
                        <Ionicons name="create-outline" size={24} color="#dc2626" />
                    </TouchableOpacity>
                )
            }} />

            <ScrollView showsVerticalScrollIndicator={false}>
                <View style={styles.profileCard}>
                    <View style={styles.avatar}>
                        <Text style={styles.avatarText}>{student?.name?.charAt(0)}</Text>
                    </View>
                    <Text style={styles.name}>{student?.name}</Text>
                    <View style={styles.badge}>
                        <Text style={styles.badgeText}>{student?.class_name}</Text>
                    </View>

                    <View style={styles.statsRow}>
                        <View style={styles.statBox}>
                            <Text style={styles.statVal}>₹{totalPaid}</Text>
                            <Text style={styles.statLabel}>Total Paid</Text>
                        </View>
                        <View style={[styles.statBox, { borderLeftWidth: 1, borderLeftColor: '#f1f5f9' }]}>
                            <Text style={styles.statVal}>{history.length}</Text>
                            <Text style={styles.statLabel}>Receipts</Text>
                        </View>
                    </View>

                    <View style={styles.detailsGrid}>
                        <View style={styles.detailItem}>
                            <Text style={styles.detailLabel}>Roll No</Text>
                            <Text style={styles.detailValue}>{student?.roll_no || 'N/A'}</Text>
                        </View>
                        <View style={styles.detailItem}>
                            <Text style={styles.detailLabel}>Phone</Text>
                            <Text style={styles.detailValue}>{student?.phone || 'N/A'}</Text>
                        </View>
                        <View style={styles.detailItem}>
                            <Text style={styles.detailLabel}>Parent</Text>
                            <Text style={styles.detailValue}>{student?.parent_name || 'N/A'}</Text>
                        </View>
                        <View style={styles.detailItem}>
                            <Text style={styles.detailLabel}>Session</Text>
                            <Text style={styles.detailValue}>{student?.session || 'N/A'}</Text>
                        </View>
                    </View>

                    <TouchableOpacity
                        style={styles.payBtn}
                        onPress={() => router.push({ pathname: '/(tabs)/collect', params: { student_id: id, name: student?.name } })}
                    >
                        <Ionicons name="wallet-outline" size={20} color="white" />
                        <Text style={styles.payBtnText}>Pay Fee Now</Text>
                    </TouchableOpacity>
                </View>

                <View style={styles.historySection}>
                    <Text style={styles.sectionTitle}>Fee History</Text>
                    {history.map((item, idx) => (
                        <View key={idx} style={styles.historyItem}>
                            <View style={styles.dateBox}>
                                <Text style={styles.day}>{new Date(item.payment_date).getDate()}</Text>
                                <Text style={styles.month}>{new Date(item.payment_date).toLocaleString('default', { month: 'short' })}</Text>
                            </View>
                            <View style={{ flex: 1, marginLeft: 15 }}>
                                <Text style={styles.feeTitle}>{item.custom_fee_name || item.category_name || 'General Fee'}</Text>
                                <Text style={styles.receiptNo}>{item.receipt_no} • {item.payment_method}</Text>
                            </View>
                            <Text style={styles.amount}>₹{item.amount}</Text>
                        </View>
                    ))}
                    {history.length === 0 && <Text style={styles.emptyText}>No fee records found</Text>}
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
    profileCard: {
        backgroundColor: '#fff',
        padding: 25,
        alignItems: 'center',
        borderBottomWidth: 1,
        borderBottomColor: '#f1f5f9',
    },
    avatar: {
        width: 80,
        height: 80,
        borderRadius: 40,
        backgroundColor: '#dc2626',
        alignItems: 'center',
        justifyContent: 'center',
        marginBottom: 15,
    },
    avatarText: {
        color: '#fff',
        fontSize: 32,
        fontWeight: '800',
    },
    name: {
        fontSize: 22,
        fontWeight: '800',
        color: '#1e293b',
    },
    badge: {
        backgroundColor: '#fee2e2',
        paddingHorizontal: 15,
        paddingVertical: 4,
        borderRadius: 20,
        marginTop: 8,
    },
    badgeText: {
        color: '#dc2626',
        fontWeight: '700',
        fontSize: 12,
    },
    statsRow: {
        flexDirection: 'row',
        marginTop: 25,
        backgroundColor: '#f8fafc',
        borderRadius: 16,
        padding: 15,
        width: '100%',
    },
    statBox: {
        flex: 1,
        alignItems: 'center',
    },
    statVal: {
        fontSize: 18,
        fontWeight: '800',
        color: '#1e293b',
    },
    statLabel: {
        fontSize: 11,
        color: '#64748b',
        textTransform: 'uppercase',
        fontWeight: '700',
        marginTop: 2,
    },
    detailsGrid: {
        flexDirection: 'row',
        flexWrap: 'wrap',
        marginTop: 25,
        gap: 20,
        justifyContent: 'center',
    },
    detailItem: {
        alignItems: 'center',
        minWidth: 80,
    },
    detailLabel: {
        fontSize: 11,
        color: '#94a3b8',
        textTransform: 'uppercase',
        fontWeight: '600',
    },
    detailValue: {
        fontSize: 14,
        color: '#334155',
        fontWeight: '700',
        marginTop: 2,
    },
    payBtn: {
        backgroundColor: '#dc2626',
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
        paddingVertical: 14,
        paddingHorizontal: 30,
        borderRadius: 12,
        marginTop: 25,
        width: '100%',
    },
    payBtnText: {
        color: '#fff',
        fontWeight: '800',
        fontSize: 16,
        marginLeft: 10,
    },
    historySection: {
        padding: 20,
    },
    sectionTitle: {
        fontSize: 18,
        fontWeight: '700',
        color: '#1e293b',
        marginBottom: 15,
    },
    historyItem: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: '#fff',
        padding: 15,
        borderRadius: 16,
        marginBottom: 12,
        borderWidth: 1,
        borderColor: '#f1f5f9',
    },
    dateBox: {
        width: 45,
        alignItems: 'center',
        borderRightWidth: 1,
        borderRightColor: '#f1f5f9',
        paddingRight: 15,
    },
    day: {
        fontSize: 18,
        fontWeight: '800',
        color: '#1e293b',
    },
    month: {
        fontSize: 10,
        color: '#64748b',
        textTransform: 'uppercase',
        fontWeight: '700',
    },
    feeTitle: {
        fontSize: 15,
        fontWeight: '700',
        color: '#1e293b',
    },
    receiptNo: {
        fontSize: 12,
        color: '#94a3b8',
        marginTop: 2,
    },
    amount: {
        fontSize: 16,
        fontWeight: '800',
        color: '#22c55e',
    },
    emptyText: {
        textAlign: 'center',
        color: '#94a3b8',
        marginTop: 20,
    }
});
