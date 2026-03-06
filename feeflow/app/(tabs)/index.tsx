import React, { useEffect, useState } from 'react';
import { StyleSheet, View, Text, ScrollView, RefreshControl, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { getDashboard } from '../../lib/api';
import { StatusBar } from 'expo-status-bar';

export default function DashboardScreen() {
    const [stats, setStats] = useState<any>(null);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);

    const loadData = async () => {
        try {
            const response = await getDashboard();
            if (response.status) {
                setStats(response.data);
            }
        } catch (error) {
            console.error(error);
        } finally {
            setLoading(false);
            setRefreshing(false);
        }
    };

    useEffect(() => {
        loadData();
    }, []);

    const onRefresh = () => {
        setRefreshing(true);
        loadData();
    };

    return (
        <ScrollView
            style={styles.container}
            refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
        >
            <StatusBar style="dark" />
            <View style={styles.header}>
                <Text style={styles.welcomeText}>Hello, Admin!</Text>
                <Text style={styles.subText}>Here is your institute summary</Text>
            </View>

            <View style={styles.statsGrid}>
                <View style={[styles.statCard, { backgroundColor: '#eff6ff' }]}>
                    <Ionicons name="people" size={24} color="#3b82f6" />
                    <Text style={styles.statLabel}>Total Students</Text>
                    <Text style={styles.statValue}>{stats?.total_students || 0}</Text>
                </View>
                <View style={[styles.statCard, { backgroundColor: '#f0fdf4' }]}>
                    <Ionicons name="cash" size={24} color="#22c55e" />
                    <Text style={styles.statLabel}>Month Collection</Text>
                    <Text style={styles.statValue}>₹{stats?.monthly_collection || 0}</Text>
                </View>
            </View>

            <View style={styles.section}>
                <Text style={styles.sectionTitle}>Recent Transactions</Text>
                {stats?.recent_transactions.map((item: any, idx: number) => (
                    <View key={idx} style={styles.transactionItem}>
                        <View style={styles.transactionIcon}>
                            <Ionicons name="receipt" size={20} color="#dc2626" />
                        </View>
                        <View style={{ flex: 1 }}>
                            <Text style={styles.studentName}>{item.student_name}</Text>
                            <Text style={styles.transactionMeta}>{item.receipt_no} • {item.payment_date}</Text>
                        </View>
                        <Text style={styles.amount}>₹{item.amount}</Text>
                    </View>
                ))}
                {(!stats?.recent_transactions || stats.recent_transactions.length === 0) && (
                    <Text style={styles.emptyText}>No recent transactions</Text>
                )}
            </View>
        </ScrollView>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: '#fff',
    },
    header: {
        padding: 20,
        backgroundColor: '#fff',
    },
    welcomeText: {
        fontSize: 24,
        fontWeight: '800',
        color: '#1e293b',
    },
    subText: {
        color: '#64748b',
        fontSize: 14,
    },
    statsGrid: {
        flexDirection: 'row',
        padding: 15,
        gap: 15,
    },
    statCard: {
        flex: 1,
        padding: 20,
        borderRadius: 16,
        alignItems: 'center',
        justifyContent: 'center',
    },
    statLabel: {
        fontSize: 12,
        color: '#64748b',
        marginTop: 8,
        fontWeight: '600',
    },
    statValue: {
        fontSize: 20,
        fontWeight: '800',
        color: '#1e293b',
        marginTop: 4,
    },
    section: {
        padding: 20,
    },
    sectionTitle: {
        fontSize: 18,
        fontWeight: '700',
        color: '#1e293b',
        marginBottom: 15,
    },
    transactionItem: {
        flexDirection: 'row',
        alignItems: 'center',
        paddingVertical: 15,
        borderBottomWidth: 1,
        borderBottomColor: '#f1f5f9',
    },
    transactionIcon: {
        width: 40,
        height: 40,
        borderRadius: 20,
        backgroundColor: '#fee2e2',
        alignItems: 'center',
        justifyContent: 'center',
        marginRight: 15,
    },
    studentName: {
        fontWeight: '700',
        fontSize: 16,
        color: '#1e293b',
    },
    transactionMeta: {
        fontSize: 12,
        color: '#64748b',
    },
    amount: {
        fontWeight: '800',
        fontSize: 16,
        color: '#22c55e',
    },
    emptyText: {
        textAlign: 'center',
        color: '#94a3b8',
        marginTop: 20,
    }
});
